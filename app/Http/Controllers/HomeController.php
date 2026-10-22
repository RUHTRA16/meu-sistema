<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::with('characters')
            ->latest()
            ->get();

        return view('home', compact('campaigns'));
    }
}
