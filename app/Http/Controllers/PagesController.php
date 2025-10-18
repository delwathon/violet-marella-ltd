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
    public function lounge() {
        return view('pages.lounge');
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
    // public function lounge() {
    //     // Get today's sales data
    //     $todaySales = \App\Models\Sale::whereDate('sale_date', today())->sum('total_amount');
    //     $todayTransactions = \App\Models\Sale::whereDate('sale_date', today())->count();
    //     $totalStock = \App\Models\Product::sum('stock_quantity');
    //     $customersServed = \App\Models\Customer::where('total_orders', '>', 0)->count();

    //     // Get categories and products
    //     $categories = \App\Models\Category::active()->ordered()->get();
    //     $products = \App\Models\Product::active()->with('category')->take(20)->get();

    //     // Get recent transactions
    //     $recentTransactions = \App\Models\Sale::with(['customer', 'saleItems.product'])
    //                                         ->latest()
    //                                         ->take(10)
    //                                         ->get();

    //     return view('pages.lounge', compact(
    //         'todaySales',
    //         'todayTransactions', 
    //         'totalStock',
    //         'customersServed',
    //         'categories',
    //         'products',
    //         'recentTransactions'
    //     ));
    // }
    // public function test() {
    //     return view('pages.test');
    // }
    // public function users() {
    //     return view('pages.users');
    // }
}
