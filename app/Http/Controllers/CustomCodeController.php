<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomCodeController extends Controller
{
    /**
     * Display a listing of custom codes.
     */
    public function index()
    {
        $codes = \App\Models\CustomCode::orderBy('priority', 'desc')->paginate(10);
        return view('custom-codes.index', compact('codes'));
    }

    /**
     * Show the form for creating a new custom code.
     */
    public function create()
    {
        return view('custom-codes.edit');
    }

    /**
     * Store a newly created custom code in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:css,js,html',
            'position' => 'required|in:head,body_start,body_end',
            'content' => 'nullable|string',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ]);

        // Default checkbox to false if missing
        $validated['is_active'] = $request->has('is_active');

        \App\Models\CustomCode::create($validated);

        return redirect()->route('custom-codes.index')
            ->with('success', 'Snippet created successfully.');
    }

    /**
     * Show the form for editing the specified custom code.
     */
    public function edit(\App\Models\CustomCode $customCode)
    {
        return view('custom-codes.edit', compact('customCode'));
    }

    /**
     * Update the specified custom code in storage.
     */
    public function update(Request $request, \App\Models\CustomCode $customCode)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:css,js,html',
            'position' => 'required|in:head,body_start,body_end',
            'content' => 'nullable|string',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ]);
        
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        $customCode->update($validated);

        return redirect()->route('custom-codes.index')
            ->with('success', 'Snippet updated successfully.');
    }

    /**
     * Remove the specified custom code from storage.
     */
    public function destroy(\App\Models\CustomCode $customCode)
    {
        $customCode->delete();

        return redirect()->route('custom-codes.index')
            ->with('success', 'Snippet deleted successfully.');
    }
}
