<?php

namespace App\Http\Controllers\AnireCraftStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnireStoreController extends Controller
{
    public function index()
    {
        $user = Auth::guard('user')->user();
        return view('pages.anire-craft-store.index', compact('user'));
    }
}