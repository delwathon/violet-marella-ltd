<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index() {
        return view('pages.index');
    }
    public function dashboard() {
        return view('pages.dashboard');
    }
    public function giftStore() {
        return view('pages.gift-store');
    }
    public function instrumentRental() {
        return view('pages.instrument-rental');
    }
    public function musicStudio() {
        return view('pages.music-studio');
    }
    public function reports() {
        return view('pages.reports');
    }
    public function settings() {
        return view('pages.settings');
    }
    public function supermarket() {
        return view('pages.supermarket');
    }
    public function test() {
        return view('pages.test');
    }
    public function users() {
        return view('pages.users');
    }
}
