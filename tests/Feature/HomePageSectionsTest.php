<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageSectionsTest extends TestCase
{
    /**
     * Test that all redesigned sections on the homepage render with HTTP 200 and proper classes.
     */
    public function test_all_homepage_sections_render_with_elevated_styles(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Hero Section
        $response->assertSee('hero-main-card');
        $response->assertSee('hero-main-swiper');
        $response->assertSee('hero-glass-badge');
        $response->assertSee('hero-dash-item');
        $response->assertSee('hero-side-card');

        // 2. Popular Categories
        $response->assertSee('popular-categories-section');
        $response->assertSee('popular-cats-swiper');
        $response->assertSee('cat-item-card');
        $response->assertSee('cat-icon-slot');
        $response->assertSee('cat-badge-counter');
        $response->assertSee('cat-label');
        $response->assertSee('slidesPerView: 3');

        // 3. Featured Flagship Products
        $response->assertSee('featured-pro-card');
        $response->assertSee('featured-brand-chip');
        $response->assertSee('featured-stock-pill');
        $response->assertSee('featured-pro-btn');

        // 4. Insta360 Promo Banner & Workflow Solutions
        $response->assertSee('insta360-promo-banner');
        $response->assertSee('promo-cta-btn');
        $response->assertSee('workflow-card');
        $response->assertSee('workflow-meta-pill');

        // 5. Double Banners
        $response->assertSee('banner-box');
        $response->assertSee('banner-pill-btn');

        // 6. Institutional & State Funding (INDH)
        $response->assertSee('state-funding-wrapper');
        $response->assertSee('indh-step-card-pro');
        $response->assertSee('indh-step-badge-num');
        $response->assertSee('indh-guarantee-pill');

        // 7. Reassurance & Client Reviews
        $response->assertSee('reassurance-box');
        $response->assertSee('client-review-box');

        // 8. Newsletter
        $response->assertSee('newsletter-banner');
        $response->assertSee('newsletter-glow');

        // Always-on WhatsApp
        $response->assertSee('whatsapp-float');
    }
}
