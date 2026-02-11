<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('items')->get();
        return view('menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|unique:menus,location',
        ]);

        Menu::create($validated);
        return redirect()->back()->with('success', 'Menu created successfully!');
    }

    public function updateItem(Request $request, Menu $menu)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        // Simple sync for now: delete all and recreate or update
        // For a more advanced version, we would use IDs and soft updates
        $menu->items()->delete();

        foreach ($request->items as $index => $itemData) {
            if (empty($itemData['label']) || empty($itemData['link'])) continue;
            
            $menu->items()->create([
                'label' => $itemData['label'],
                'link' => $itemData['link'],
                'order' => $index,
                'target' => $itemData['target'] ?? '_self',
            ]);
        }

        return redirect()->back()->with('success', 'Menu items updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->back()->with('success', 'Menu deleted successfully!');
    }
}
