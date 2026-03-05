<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySlides;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanySlidersController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/CompanySlider/Index', [
            'slides' => CompanySlides::orderBy('sort_order')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'       => 'required|in:image,text',
            'title'      => 'nullable|string',
            'image'      => 'nullable|string',
            'link'       => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        CompanySlides::create($data);

        return back()->with('success', 'Company slide created');
    }

    public function update(Request $request, CompanySlides $companySlide)
    {
        $data = $request->validate([
            'type'       => 'required|in:image,text',
            'title'      => 'nullable|string',
            'image'      => 'nullable|string',
            'link'       => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $companySlide->update($data);

        return back()->with('success', 'Company slide updated');
    }

    public function destroy(CompanySlides $companySlide)
    {
        $companySlide->delete();

        return back()->with('success', 'Company slide deleted');
    }
}
