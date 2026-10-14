<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactPageTest extends TestCase
{
    public function test_contact_page_renders_with_new_design_elements(): void
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);

        // Hero and branding elements matching the new design
        $response->assertSee('contact-hero-section', false);
        $response->assertSee('contact-hero-glow', false);
        $response->assertSee('contact-hero-pill', false);
        $response->assertSee('about-pulse-dot', false);
        $response->assertSee('about-feature-chip', false);

        // Showroom address & contact info
        $response->assertSee('01 Rue 102, Hay Wiam, Al Oulfa, Casablanca 20220', false);
        $response->assertSee('06 29 03 57 77', false);
        $response->assertSee('contact@winashop.com', false);
        $response->assertSee('ICE', false);

        // Modern form and cards
        $response->assertSee('contact-info-card', false);
        $response->assertSee('contact-wa-card', false);
        $response->assertSee('contact-form-card', false);
        $response->assertSee('custom-faq-accordion', false);

        // Mobile touch-friendly responsive button classes
        $response->assertSee('w-100 w-sm-auto', false);
    }

    public function test_contact_form_submission_processes_successfully(): void
    {
        $postData = [
            'name'    => 'Yassine Production',
            'email'   => 'yassine@studiomaroc.ma',
            'phone'   => '0629035777',
            'subject' => 'corporate_quote',
            'message' => 'Demande de devis pour un kit de tournage Sony FX3 avec facture ICE.',
        ];

        $response = $this->post(route('contact.send'), $postData);
        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');
    }

    public function test_contact_page_renders_cleanly_in_arabic(): void
    {
        session(['locale' => 'ar']);
        app()->setLocale('ar');

        $response = $this->get(route('contact'));
        $response->assertStatus(200);

        // Arabic headlines and labels
        $response->assertSee('تواصلوا مع خبرائنا في', false);
        $response->assertSee('الدار البيضاء', false);

        // Clean up
        session()->forget('locale');
        app()->setLocale('fr');
    }
}
