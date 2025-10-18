<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Index Page.
     */
    public function index()
    {
        $user = Auth::guard('user')->user();
        
        return view('pages.settings', compact('user'));
    }
}
