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

    public function ourWork()
    {
        return Inertia::render('Landing/Gsap/OurWork/Index');
    }

    public function getInTuch()
    {
        return Inertia::render('Landing/Gsap/GetInTuch/Index');
    }

    public function OurTeam()
    {
        return Inertia::render('Landing/Gsap/OurTeam/Index');
    }

    public function PortfolioDetail()
    {
        return Inertia::render('Landing/Gsap/Portfolio/Detail');
    }
}
