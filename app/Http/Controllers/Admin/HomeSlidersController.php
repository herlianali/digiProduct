<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSliders;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeSlidersController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/HomeSlider/Index', [
            'sliders' => HomeSliders::orderBy('sort_order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string',
            'subtitle'   => 'nullable|string',
            'image'      => 'required|string',
            'link'       => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        HomeSliders::create($data);

        return back()->with('success', 'Home slider created');
    }

    public function update(Request $request, HomeSliders $homeSlider)
    {
        $data = $request->validate([
            'title'      => 'nullable|string',
            'subtitle'   => 'nullable|string',
            'image'      => 'nullable|string',
            'link'       => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $homeSlider->update($data);

        return back()->with('success', 'Home slider updated');
    }

    public function destroy(HomeSliders $homeSlider)
    {
        $homeSlider->delete();

        return back()->with('success', 'Home slider deleted');
    }
}
