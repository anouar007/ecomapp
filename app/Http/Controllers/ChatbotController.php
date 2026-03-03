<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ChatbotController extends Controller
{
    /**
     * Handle a chatbot message.
     * Priority: Ollama (local dev) → Groq (production / shared hosting)
     */
    public function ask(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        // Rate limit: 15 requests per minute per user
        $key = 'chatbot:' . (auth()->id() ?? 'guest');
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return response()->json([
                'reply' => "Vous envoyez trop de messages. Veuillez patienter une minute."
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $appName = setting('app_name', 'cette boutique');
        $phone   = setting('company_phone', '');
        $email   = setting('company_email', '');
        $isAdmin = auth()->user()?->hasRole('Admin') ?? false;

        $systemPrompt = $isAdmin
            ? "Tu es un assistant IA expert pour les administrateurs de {$appName}, une boutique e-commerce d'équipements d'impression grand format au Maroc. Tu aides avec : gestion des produits, commandes, coupons, inventaire, clients, rapports, paramètres et l'utilisation du tableau de bord. Réponds en français, de façon concise (max 4 phrases). Si tu ne sais pas, dis-le honnêtement."
            : "Tu es un assistant client pour {$appName}, une boutique d'équipements d'impression grand format au Maroc. Infos clés: livraison J+1 grandes villes, installation gratuite, formation incluse, garantie 1 an, paiement par virement/chèque/facilités. Contact: {$phone} / {$email}. Réponds en français, de façon concise (max 4 phrases).";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $request->message],
        ];

        // ── 1. Ollama (local development) ───────────────────────────────────
        try {
            $ollamaResponse = Http::timeout(30)->post('http://localhost:11434/api/chat', [
                'model'    => config('services.ollama.model', 'llama3.2'),
                'messages' => $messages,
                'stream'   => false,
            ]);

            if ($ollamaResponse->successful()) {
                $reply = $ollamaResponse->json('message.content', '');
                if (!empty(trim($reply))) {
                    return response()->json(['reply' => trim($reply)]);
                }
            }
        } catch (\Exception $e) {
            // Ollama not available — try Groq
        }

        // ── 2. Groq (shared hosting / production fallback) ──────────────────
        $groqKey = config('services.groq.key');
        if (!empty($groqKey)) {
            try {
                $groqResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$groqKey}",
                    'Content-Type'  => 'application/json',
                ])->timeout(20)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages'   => $messages,
                    'max_tokens' => 512,
                ]);

                if ($groqResponse->successful()) {
                    $reply = $groqResponse->json('choices.0.message.content', '');
                    if (!empty(trim($reply))) {
                        return response()->json(['reply' => trim($reply)]);
                    }
                }
            } catch (\Exception $e) {
                // Groq failed
            }
        }

        return response()->json([
            'reply' => "Le service IA est temporairement indisponible. Contactez-nous au {$phone} ou par email à {$email}."
        ]);
    }
}
