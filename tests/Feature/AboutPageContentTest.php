<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageContentTest extends TestCase
{
    public function test_about_page_renders_with_official_winashop_content(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);

        // Official headlines and data extracted from winashop.com/a-propos/
        $response->assertSee('La technologie au service de la', false);
        $response->assertSee('créativité', false);
        $response->assertSee('Votre expert en équipements photo et vidéo à Casablanca', false);
        $response->assertSee('Notre Engagement', false);
        $response->assertSee('Un service sur mesure pour vos projets créatifs', false);

        // Showroom address & contact
        $response->assertSee('01 Rue 102, Hay Wiam, Al Oulfa, Casablanca 20220', false);
        $response->assertSee('06 29 03 57 77', false);

        // Core official partner brands
        $response->assertSee('SONY');
        $response->assertSee('CANON');
        $response->assertSee('NIKON');
        $response->assertSee('FUJIFILM');
        $response->assertSee('DJI PRO');
        $response->assertSee('AMARAN');
        $response->assertSee('APUTURE');
    }

    public function test_a_propos_url_redirects_cleanly_to_about(): void
    {
        $response = $this->get('/a-propos');
        $response->assertRedirect(route('about'));
    }

    public function test_about_page_renders_cleanly_in_arabic(): void
    {
        session(['locale' => 'ar']);
        app()->setLocale('ar');

        $response = $this->get(route('about'));
        $response->assertStatus(200);

        // Arabic official headline
        $response->assertSee('التكنولوجيا في خدمة', false);
        $response->assertSee('الإبداع', false);
        $response->assertSee('الدار البيضاء', false);

        // Clean up
        session()->forget('locale');
        app()->setLocale('fr');
    }

    public function test_about_page_contains_responsive_and_accessible_markup(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);

        // Responsive grid and components
        $response->assertSee('about-check-card', false);
        $response->assertSee('about-cat-body', false);
        $response->assertSee('about-cat-link', false);
        $response->assertSee('about-showroom-visual', false);
        $response->assertSee('about-showroom-badge', false);

        // Mobile touch-friendly responsive button classes
        $response->assertSee('w-100 w-sm-auto', false);
    }
}
