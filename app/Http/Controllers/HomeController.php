<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\Reel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the landing page
     */
    public function index()
    {
        $profile = DoctorProfile::with('user')->first();
        $services = Service::where('is_active', true)->get();
        $reels = Reel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('index', compact('profile', 'services', 'reels'));
    }
}
