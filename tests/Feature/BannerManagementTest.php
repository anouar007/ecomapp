<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BannerManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve or create an admin user
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@speed.com'],
            [
                'name'     => 'Admin Tester',
                'password' => bcrypt('password'),
            ]
        );
    }

    /**
     * Test that an unauthenticated user is redirected to login.
     */
    public function test_unauthenticated_user_cannot_access_banners_dashboard(): void
    {
        $response = $this->get('/banners');
        $response->assertRedirect('/login');
    }

    /**
     * Test that an authenticated user can view the banners dashboard.
     */
    public function test_authenticated_admin_can_view_banners_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/banners');

        $response->assertStatus(200);
        $response->assertSee('Slider & Bannières');
        $response->assertSee('Slider Principal');
        $response->assertSee('Encadrés Latéraux Hero');
        $response->assertSee('Bannière Promo (Large)');
    }

    /**
     * Test creating a new slide in the hero slider.
     */
    public function test_can_create_new_hero_slide(): void
    {
        $slideData = [
            'title'        => 'Test Nouvelle Diapositive 8K',
            'badge'        => 'NOUVEAU 2026',
            'description'  => 'Description de test pour la caméra cinéma 8K.',
            'link'         => '/shop?q=8k',
            'sort_order'   => 5,
            'status'       => 'active',
            'image_preset' => 'images/camera/hero_cinema_rig.jpg',
        ];

        $response = $this->actingAs($this->adminUser)->post('/banners', $slideData);

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseHas('banners', [
            'position' => 'main_hero',
            'title'    => 'Test Nouvelle Diapositive 8K',
            'badge'    => 'NOUVEAU 2026',
            'status'   => 'active',
        ]);

        // Verify that the homepage reflects this new slide
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Test Nouvelle Diapositive 8K');
    }

    /**
     * Test updating an existing slide.
     */
    public function test_can_update_existing_hero_slide(): void
    {
        $slide = Banner::create([
            'position'    => 'main_hero',
            'title'       => 'Slide Initial Avant Modif',
            'badge'       => 'INITIAL',
            'description' => 'Desc Initiale',
            'link'        => '/shop',
            'sort_order'  => 10,
            'status'      => 'active',
            'image'       => 'images/camera/hero_winashop.jpg',
        ]);

        $updateData = [
            'title'       => 'Slide Mis à Jour Avec Succès',
            'badge'       => 'ÉDITION SPÉCIALE',
            'description' => 'Desc modifiée via le tableau de bord.',
            'link'        => '/shop?updated=1',
            'sort_order'  => 2,
            'status'      => 'active',
        ];

        $response = $this->actingAs($this->adminUser)->put("/banners/{$slide->id}", $updateData);

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseHas('banners', [
            'id'    => $slide->id,
            'title' => 'Slide Mis à Jour Avec Succès',
            'badge' => 'ÉDITION SPÉCIALE',
        ]);
    }

    /**
     * Test updating the top side card (side_top).
     */
    public function test_can_update_side_top_box(): void
    {
        $sideTopData = [
            'title'        => 'Sony FX3 Cinema Line',
            'badge'        => 'FULL FRAME 4K 120P',
            'button_text'  => 'Explorer la gamme',
            'link'         => '/shop?q=Sony+FX3',
            'status'       => 'active',
            'image_preset' => 'images/camera/hero_cinema_rig.jpg',
        ];

        $response = $this->actingAs($this->adminUser)->post('/banners/position/side_top', $sideTopData);

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseHas('banners', [
            'position'    => 'side_top',
            'title'       => 'Sony FX3 Cinema Line',
            'button_text' => 'Explorer la gamme',
        ]);

        // Verify homepage shows the updated side card
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Sony FX3 Cinema Line');
        $homeResponse->assertSee('Explorer la gamme');
    }

    /**
     * Test updating the bottom side card (side_bottom).
     */
    public function test_can_update_side_bottom_box(): void
    {
        $sideBottomData = [
            'title'        => 'Aputure 600d Pro Light',
            'badge'        => 'PRO STUDIO 600W',
            'button_text'  => 'Découvrir la lumière',
            'link'         => '/shop?q=Aputure',
            'status'       => 'active',
            'image_preset' => 'images/camera/hero_lens_optics.jpg',
        ];

        $response = $this->actingAs($this->adminUser)->post('/banners/position/side_bottom', $sideBottomData);

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseHas('banners', [
            'position'    => 'side_bottom',
            'title'       => 'Aputure 600d Pro Light',
            'button_text' => 'Découvrir la lumière',
        ]);

        // Verify homepage shows the updated side card
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Aputure 600d Pro Light');
    }

    /**
     * Test updating the wide middle promo banner (after Notre sélection de produits).
     */
    public function test_can_update_wide_middle_promo_banner(): void
    {
        $promoData = [
            'title'        => 'Offre Black Friday Audiovisuel 2026',
            'badge'        => 'VENTE FLASH',
            'subtitle'     => 'ÉQUIPEMENT STUDIO & TOURNAGE',
            'description'  => 'Jusqu à 35% de réduction immédiate sur tous les stabilisateurs et optiques pro.',
            'button_text'  => 'Profiter des Remises',
            'features'     => "LIVRAISON EXPRESS 24H\nGARANTIE CONSTRUCTEUR 2 ANS\nFACILITÉ DE PAIEMENT",
            'link'         => '/shop?promo=blackfriday',
            'status'       => 'active',
            'image_preset' => 'images/camera/banner_insta360_promo.jpg',
        ];

        $response = $this->actingAs($this->adminUser)->post('/banners/position/wide_middle', $promoData);

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseHas('banners', [
            'position'    => 'wide_middle',
            'title'       => 'Offre Black Friday Audiovisuel 2026',
            'button_text' => 'Profiter des Remises',
        ]);

        // Verify homepage reflects the updated promotional banner
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Offre Black Friday Audiovisuel 2026');
        $homeResponse->assertSee('Profiter des Remises');
        $homeResponse->assertSee('LIVRAISON EXPRESS 24H');
    }

    /**
     * Test deleting a hero slide.
     */
    public function test_can_delete_hero_slide(): void
    {
        $slide = Banner::create([
            'position'    => 'main_hero',
            'title'       => 'Slide à supprimer',
            'badge'       => 'TEMP',
            'sort_order'  => 99,
            'status'      => 'active',
            'image'       => 'images/camera/hero_winashop.jpg',
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/banners/{$slide->id}");

        $response->assertRedirect(route('banners.index'));
        $this->assertDatabaseMissing('banners', [
            'id' => $slide->id,
        ]);
    }
}
