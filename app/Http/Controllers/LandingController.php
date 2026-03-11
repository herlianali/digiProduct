<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        return Inertia::render('Landing/Index');
    }

    public function gsap()
    {
        return Inertia::render('Landing/Gsap/Index');
    }
}
