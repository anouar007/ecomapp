<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestFirewall
{
    /**
     * Known automated vulnerability scanner user-agent signatures.
     */
    protected array $blockedUserAgents = [
        'sqlmap',
        'nikto',
        'nessus',
        'acunetix',
        'netsparker',
        'appscan',
        'masscan',
        'zgrab',
        'dirbuster',
        'gobuster',
        'wpscan',
        'havij',
        'arachni',
        'hydra',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip() ?? '0.0.0.0';
        $cache = Cache::store('file');
        $banKey = 'waf_ban_' . md5($ip);

        if ($cache->has($banKey)) {
            return $this->deny($request, 'Too many suspicious requests.');
        }

        // 1. Block known automated scanner user-agents
        $ua = strtolower((string) $request->userAgent());
        if ($ua !== '') {
            foreach ($this->blockedUserAgents as $sig) {
                if (str_contains($ua, $sig)) {
                    return $this->blockAndStrike($request, $ip, 'scanner_ua:' . $sig);
                }
            }
        }

        // 2. Block path traversal & sensitive file probes
        $rawUri = urldecode((string) $request->getRequestUri());
        if (preg_match('/(\.\.[\/\\\\]|\/\.env\b|\/\.git\b|\/wp-login\.php|\/wp-admin|\/xmlrpc\.php|\/phpmyadmin|\/etc\/passwd)/i', $rawUri)) {
            return $this->blockAndStrike($request, $ip, 'path_probe');
        }

        // 3. Inspect query parameters for SQLi / XSS payloads
        $queryValues = $this->flattenValues($request->query->all());
        foreach ($queryValues as $val) {
            if ($this->containsAttackPayload($val)) {
                return $this->blockAndStrike($request, $ip, 'query_payload');
            }
        }

        // 4. Inspect POST/PUT/PATCH body on public (unauthenticated) requests
        if (!$request->user() && $request->isMethod('POST', 'PUT', 'PATCH')) {
            $bodyValues = $this->flattenValues($request->except(['_token', '_method', 'password', 'password_confirmation']));
            foreach ($bodyValues as $val) {
                if ($this->containsAttackPayload($val)) {
                    return $this->blockAndStrike($request, $ip, 'body_payload');
                }
            }
        }

        return $next($request);
    }

    /**
     * Flatten nested request inputs into string values.
     */
    protected function flattenValues(array $input): array
    {
        $values = [];
        array_walk_recursive($input, function ($item) use (&$values) {
            if (is_string($item) && $item !== '') {
                $values[] = $item;
            }
        });

        return $values;
    }

    /**
     * Detect SQL injection and XSS payloads.
     */
    protected function containsAttackPayload(string $value): bool
    {
        return (bool) preg_match(
            '/(extractvalue\s*\(|updatexml\s*\(|union\s+(all\s+)?select\b|select\s+.+\s+from\b|elt\s*\(\s*\d+|concat\s*\(\s*0x|order\s+by\s+\d+\s*--|sleep\s*\(\s*\d+\s*\)|benchmark\s*\(\s*\d+|information_schema\b|<\s*script\b|javascript\s*:|onerror\s*=|onload\s*=)/i',
            $value
        );
    }

    /**
     * Record a strike, temporarily ban repeat offenders, log, and return 403.
     */
    protected function blockAndStrike(Request $request, string $ip, string $reason): Response
    {
        try {
            $cache = Cache::store('file');
            $strikeKey = 'waf_strikes_' . md5($ip);
            $strikes = (int) $cache->get($strikeKey, 0) + 1;
            $cache->put($strikeKey, $strikes, now()->addMinutes(10));

            if ($strikes >= 5) {
                $cache->put('waf_ban_' . md5($ip), true, now()->addMinutes(30));
            }

            Log::warning('RequestFirewall blocked request', [
                'ip' => $ip,
                'reason' => $reason,
                'strikes' => $strikes,
                'method' => $request->method(),
                'uri' => mb_substr((string) $request->getRequestUri(), 0, 250),
                'ua' => mb_substr((string) $request->userAgent(), 0, 150),
            ]);
        } catch (\Throwable $e) {
            // Never let logging failure bypass blocking
        }

        return $this->deny($request, 'Forbidden');
    }

    protected function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        return response($message, 403);
    }
}
