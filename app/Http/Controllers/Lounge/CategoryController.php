<?php

namespace App\Http\Controllers\Lounge;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = Category::withCount('products');
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $categories = $query->ordered()->paginate(20);
        
        return view('pages.lounge.categories.index', compact('user', 'categories'));
    }
    
    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        
        return view('pages.lounge.categories.create', compact('user'));
    }
    
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'in:on,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $data = $request->except('image');
            $data['is_active'] = $request->has('is_active');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $data['image'] = $imagePath;
            }
            
            Category::create($data);
            
            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified category
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $category = Category::with('products')->findOrFail($id);
        
        return view('pages.lounge.categories.show', compact('user', 'category'));
    }
    
    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $category = Category::findOrFail($id);
        
        return view('pages.lounge.categories.edit', compact('user', 'category'));
    }
    
    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'in:on,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $data = $request->except('image');
            $data['is_active'] = $request->has('is_active');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                
                $imagePath = $request->file('image')->store('categories', 'public');
                $data['image'] = $imagePath;
            }
            
            $category->update($data);
            
            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return back()->with('error', 'Cannot delete category with existing products. Move products to another category first.');
            }
            
            // Delete image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            $category->delete();
            
            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all active categories (AJAX)
     */
    public function getActive()
    {
        $categories = Category::active()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}