<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductPlacementController extends Controller
{
    public function index()
    {
        return Inertia::render('Landing/Gsap/Product/Index');
    }
}
