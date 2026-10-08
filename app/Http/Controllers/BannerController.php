<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display the banners and hero slider management dashboard.
     */
    public function index()
    {
        $mainHeroSlides = Banner::where('position', 'main_hero')
            ->orderBy('sort_order', 'asc')
            ->get();

        $sideTop = Banner::firstOrNew(
            ['position' => 'side_top'],
            [
                'title'       => 'DJI Osmo Pocket 4',
                'badge'       => 'GIMBAL 4K COMPACT',
                'button_text' => 'Acheter',
                'image'       => 'images/camera/banner_osmo_pocket.jpg',
                'link'        => '/shop?q=Osmo+Pocket',
                'status'      => 'active',
                'sort_order'  => 1,
            ]
        );

        $sideBottom = Banner::firstOrNew(
            ['position' => 'side_bottom'],
            [
                'title'       => 'GODOX AD800 PRO',
                'badge'       => 'OUTDOOR FLASH 800W',
                'button_text' => 'Découvrez la GODOX AD800 PRO',
                'image'       => 'images/camera/banner_godox_ad800.jpg',
                'link'        => '/shop?q=Godox+AD800',
                'status'      => 'active',
                'sort_order'  => 1,
            ]
        );

        $wideMiddle = Banner::firstOrNew(
            ['position' => 'wide_middle'],
            [
                'title'       => 'Promotion Exclusive sur Insta360',
                'badge'       => 'PRODUIT TENDANCE',
                'subtitle'    => 'PROMOTION EXCLUSIVE',
                'description' => "Capturez l'impossible avec les caméras d'action et 360° les plus innovantes du marché. Remises jusqu'à 25% disponibles sur le showroom.",
                'button_text' => 'Voir la Promotion',
                'features'    => "VIDÉO 360° IMMERSIVE\nSTABILISATION AVANCÉE\nRÉSOLUTION 5.7K ULTRA HD",
                'image'       => 'images/camera/banner_insta360_promo.jpg',
                'link'        => '/shop?category=accessoires-insta360',
                'status'      => 'active',
                'sort_order'  => 1,
            ]
        );

        $presetImages = [
            'images/camera/hero_winashop.jpg'        => 'Hero DJI Osmo 360 (Original)',
            'images/camera/hero_cinema_rig.jpg'      => 'Cinéma Rig Pro & Moniteur',
            'images/camera/hero_lens_optics.jpg'     => 'Objectifs & Optiques Studio',
            'images/camera/hero_studio_crew.jpg'     => 'Plateau Studio & Tournage',
            'images/camera/banner_osmo_pocket.jpg'   => 'Bannière Osmo Pocket',
            'images/camera/banner_godox_ad800.jpg'   => 'Bannière Flash Godox AD800',
            'images/camera/banner_insta360_promo.jpg'=> 'Bannière Promo Insta360',
            'images/camera/banner_mic_sound.jpg'     => 'Bannière Audio & Microphones',
            'images/camera/banner_studio_lighting.jpg' => 'Bannière Éclairage Studio',
            'images/camera/cat_cameras.jpg'          => 'Rayon Caméras Hybrides',
            'images/camera/cat_lighting.jpg'         => 'Rayon Éclairage COB',
            'images/camera/cat_drones.jpg'           => 'Rayon Drones & Aérien',
            'images/camera/cat_audio.jpg'            => 'Rayon Son & Audio Pro',
            'images/camera/shop_hero_banner.jpg'     => 'Bannière Boutique Principale',
        ];

        return view('banners.index', compact(
            'mainHeroSlides',
            'sideTop',
            'sideBottom',
            'wideMiddle',
            'presetImages'
        ));
    }

    /**
     * Store a new hero slider slide.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'badge'        => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'link'         => 'nullable|string|max:255',
            'sort_order'   => 'nullable|integer',
            'status'       => 'required|in:active,inactive',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:8192',
            'image_preset' => 'nullable|string|max:255',
        ]);

        $imagePath = $request->image_preset ?: 'images/camera/hero_winashop.jpg';

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('banners', 'public');
        }

        Banner::create([
            'position'    => 'main_hero',
            'title'       => $validated['title'],
            'badge'       => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'link'        => $validated['link'] ?? '/shop',
            'sort_order'  => $validated['sort_order'] ?? (Banner::where('position', 'main_hero')->max('sort_order') + 1),
            'status'      => $validated['status'],
            'image'       => $imagePath,
        ]);

        return redirect()->route('banners.index')
            ->with('success', 'Nouvelle diapositive ajoutée au slider héroïque avec succès !');
    }

    /**
     * Update an existing banner/slide.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'        => 'nullable|string|max:255',
            'badge'        => 'nullable|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'button_text'  => 'nullable|string|max:255',
            'link'         => 'nullable|string|max:255',
            'features'     => 'nullable|string|max:2000',
            'sort_order'   => 'nullable|integer',
            'status'       => 'nullable|in:active,inactive',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:8192',
            'image_preset' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image_file')) {
            // Delete old file if stored in public storage
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $request->file('image_file')->store('banners', 'public');
        } elseif (!empty($request->image_preset)) {
            $banner->image = $request->image_preset;
        }

        $banner->title       = $validated['title'] ?? $banner->title;
        $banner->badge       = $validated['badge'] ?? $banner->badge;
        $banner->subtitle    = $validated['subtitle'] ?? $banner->subtitle;
        $banner->description = $validated['description'] ?? $banner->description;
        $banner->button_text = $validated['button_text'] ?? $banner->button_text;
        $banner->link        = $validated['link'] ?? $banner->link;
        $banner->features    = $validated['features'] ?? $banner->features;
        $banner->status      = $validated['status'] ?? $banner->status;
        if (isset($validated['sort_order'])) {
            $banner->sort_order = $validated['sort_order'];
        }

        $banner->save();

        return redirect()->route('banners.index')
            ->with('success', 'Bannière mise à jour avec succès !');
    }

    /**
     * Delete a slide or banner.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('banners.index')
            ->with('success', 'Diapositive supprimée avec succès.');
    }

    /**
     * Save/update a fixed position banner (side_top, side_bottom, wide_middle).
     */
    public function savePosition(Request $request, string $position)
    {
        $allowedPositions = ['side_top', 'side_bottom', 'wide_middle'];
        if (!in_array($position, $allowedPositions, true)) {
            abort(404);
        }

        $validated = $request->validate([
            'title'        => 'nullable|string|max:255',
            'badge'        => 'nullable|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:2000',
            'button_text'  => 'nullable|string|max:255',
            'link'         => 'nullable|string|max:255',
            'features'     => 'nullable|string|max:2000',
            'status'       => 'required|in:active,inactive',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:8192',
            'image_preset' => 'nullable|string|max:255',
        ]);

        $banner = Banner::firstOrNew(['position' => $position]);

        if ($request->hasFile('image_file')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->image = $request->file('image_file')->store('banners', 'public');
        } elseif (!empty($request->image_preset)) {
            $banner->image = $request->image_preset;
        }

        $banner->title       = $validated['title'] ?? $banner->title;
        $banner->badge       = $validated['badge'] ?? $banner->badge;
        $banner->subtitle    = $validated['subtitle'] ?? $banner->subtitle;
        $banner->description = $validated['description'] ?? $banner->description;
        $banner->button_text = $validated['button_text'] ?? $banner->button_text;
        $banner->link        = $validated['link'] ?? $banner->link;
        $banner->features    = $validated['features'] ?? $banner->features;
        $banner->status      = $validated['status'];
        $banner->save();

        $names = [
            'side_top'    => 'Encadré supérieur droit',
            'side_bottom' => 'Encadré inférieur droit',
            'wide_middle' => 'Bannière promotionnelle principale',
        ];

        $boxName = $names[$position] ?? 'Encadré';

        return redirect()->route('banners.index')
            ->with('success', "{$boxName} mis à jour avec succès !");
    }
}
