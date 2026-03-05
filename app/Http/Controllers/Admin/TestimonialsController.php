<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonials;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/UserTestimoni/Index', [
            'testimonials' => Testimonials::orderBy('sort_order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'role'       => 'nullable|string',
            'avatar'     => 'nullable|string',
            'rating'     => 'nullable|integer|min:1|max:5',
            'message'    => 'required|string',
            'is_active'  => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        Testimonials::create($data);

        return back()->with('success', 'Testimonial created');
    }

    public function update(Request $request, Testimonials $testimonial)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'role'       => 'nullable|string',
            'avatar'     => 'nullable|string',
            'rating'     => 'nullable|integer|min:1|max:5',
            'message'    => 'required|string',
            'is_active'  => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $testimonial->update($data);

        return back()->with('success', 'Testimonial updated');
    }

    public function destroy(Testimonials $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted');
    }
}
