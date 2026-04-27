<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        return Inertia::render('Landing/Gsap/Index');
    }

    public function gsap()
    {
        return Inertia::render('Landing/Gsap/Index');
    }

    public function ProductDetail($id)
    {
        return Inertia::render('Landing/Gsap/Product/Detail');
    }

    public function GetInTuch()
    {
        return Inertia::render('Landing/Gsap/GetInTuch/Index');
    }

    public function OurTeam()
    {
        return Inertia::render('Landing/Gsap/OurTeam/Index');
    }

    public function PortfolioDetail($id)
    {
        return Inertia::render('Landing/Gsap/Portfolio/Detail');
    }
}
