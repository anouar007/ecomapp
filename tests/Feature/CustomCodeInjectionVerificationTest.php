<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CustomCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomCodeInjectionVerificationTest extends TestCase
{
    public function test_custom_codes_are_injected_into_frontend_layout()
    {
        // 1. Setup Custom Codes
        $headCode = CustomCode::create([
            'title' => 'Head Tracking',
            'type' => 'js',
            'position' => 'head',
            'content' => "console.log('Head Injected');",
            'is_active' => true,
            'priority' => 10
        ]);

        $bodyStartCode = CustomCode::create([
            'title' => 'Body Start Banner',
            'type' => 'html',
            'position' => 'body_start',
            'content' => "<div id='body-start-marker'>Start</div>",
            'is_active' => true,
            'priority' => 10
        ]);

        $bodyEndCode = CustomCode::create([
            'title' => 'Footer Scripts',
            'type' => 'js',
            'position' => 'body_end',
            'content' => "console.log('Footer Injected');",
            'is_active' => true,
            'priority' => 10
        ]);

        // 2. Visit Frontend Page
        $response = $this->get(route('home'));
        
        // 3. Assert Content
        $response->assertStatus(200);
        
        // Head Check
        $response->assertSee("console.log('Head Injected');", false);
        
        // Body Start Check
        $response->assertSee("<div id='body-start-marker'>Start</div>", false);
        
        // Body End Check
        $response->assertSee("console.log('Footer Injected');", false);
    }
}
