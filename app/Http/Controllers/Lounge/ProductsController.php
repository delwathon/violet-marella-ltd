<?php

namespace App\Http\Controllers\Lounge;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $query = Product::with('category');
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Stock status filter
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->lowStock();
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }
        
        // Status filter
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::active()->ordered()->get();
        
        // Low stock count
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        
        return view('pages.lounge.products.index', compact(
            'user',
            'products',
            'categories',
            'lowStockCount',
            'outOfStockCount'
        ));
    }
    
    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $user = Auth::guard('user')->user();
        $categories = Category::active()->ordered()->get();
        
        return view('pages.lounge.products.create', compact('user', 'categories'));
    }
    
    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'maximum_stock_level' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'brand' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'track_stock' => 'in:on,NULL',
            'is_active' => 'in:on,NULL',
            'is_featured' => 'in:on,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        DB::beginTransaction();
        
        try {
            $data = $request->except('image');
            $data['track_stock'] = $request->has('track_stock');
            $data['is_active'] = $request->has('is_active');
            $data['is_featured'] = $request->has('is_featured');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }
            
            $product = Product::create($data);
            
            // Log initial inventory
            if ($product->stock_quantity > 0) {
                InventoryLog::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::guard('user')->id(),
                    'action_type' => 'purchase',
                    'quantity_change' => $product->stock_quantity,
                    'previous_stock' => 0,
                    'new_stock' => $product->stock_quantity,
                    'reason' => 'Initial stock',
                    'action_date' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified product
     */
    public function show($id)
    {
        $user = Auth::guard('user')->user();
        $product = Product::with(['category', 'saleItems', 'inventoryLogs'])->findOrFail($id);
        
        // Get recent sales
        $recentSales = $product->saleItems()
            ->with('sale')
            ->latest()
            ->limit(10)
            ->get();
        
        // Get inventory history
        $inventoryHistory = $product->inventoryLogs()
            ->with('staff')
            ->latest()
            ->limit(20)
            ->get();
        
        return view('pages.lounge.products.show', compact('user', 'product', 'recentSales', 'inventoryHistory'));
    }
    
    /**
     * Show the form for editing the specified product
     */
    public function edit($id)
    {
        $user = Auth::guard('user')->user();
        $product = Product::findOrFail($id);
        $categories = Category::active()->ordered()->get();
        
        return view('pages.lounge.products.edit', compact('user', 'product', 'categories'));
    }
    
    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'maximum_stock_level' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $id,
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'brand' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'track_stock' => 'in:on,NULL',
            'is_active' => 'in:on,NULL',
            'is_featured' => 'in:on,NULL',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        DB::beginTransaction();
        
        try {
            $data = $request->except('image');
            $data['track_stock'] = $request->has('track_stock');
            $data['is_active'] = $request->has('is_active');
            $data['is_featured'] = $request->has('is_featured');
            
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }
            
            // Track stock changes
            $previousStock = $product->stock_quantity;
            $newStock = $data['stock_quantity'];
            
            $product->update($data);
            
            // Log stock change if quantity changed
            if ($previousStock != $newStock) {
                InventoryLog::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::guard('user')->id(),
                    'action_type' => 'adjustment',
                    'quantity_change' => $newStock - $previousStock,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reason' => 'Stock update',
                    'action_date' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $product->delete();
            
            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
    
    /**
     * Adjust product stock
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'quantity_change' => 'required|integer|not_in:0',
            'action_type' => 'required|in:purchase,adjustment,damage,expiry,return',
            'reason' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);
        
        $product = Product::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $previousStock = $product->stock_quantity;
            $newStock = max(0, $previousStock + $request->quantity_change);
            
            // Update product stock
            $product->update(['stock_quantity' => $newStock]);
            
            // Log inventory change
            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::guard('user')->id(),
                'action_type' => $request->action_type,
                'quantity_change' => $request->quantity_change,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $request->unit_cost,
                'reason' => $request->reason,
                'action_date' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'product' => $product->fresh()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get product by barcode (AJAX)
     */
    public function getByBarcode(Request $request)
    {
        $barcode = $request->get('barcode');
        
        $product = Product::where('barcode', $barcode)
            ->with('category')
            ->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }
    
    /**
     * Export products to CSV
     */
    public function export()
    {
        $products = Product::with('category')->get();
        
        $filename = 'products_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'SKU', 'Name', 'Category', 'Price', 'Cost Price', 
                'Stock', 'Min Stock', 'Unit', 'Status'
            ]);
            
            // Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category->name ?? 'N/A',
                    $product->price,
                    $product->cost_price,
                    $product->stock_quantity,
                    $product->minimum_stock_level,
                    $product->unit,
                    $product->is_active ? 'Active' : 'Inactive'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get low stock products (AJAX)
     */
    public function getLowStock()
    {
        $products = Product::lowStock()
            ->with('category')
            ->get();
        
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
    
    /**
     * Show bulk upload page
     */
    public function bulkUploadPage()
    {
        $user = Auth::guard('user')->user();
        $categories = Category::active()->ordered()->get();
        
        return view('pages.lounge.products.bulk-upload', compact('user', 'categories'));
    }
    
    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'product_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Headers with instructions in first row
            fputcsv($file, [
                'name (required)',
                'sku (optional - auto-generated)',
                'barcode (optional)',
                'category_name (required)',
                'price (required)',
                'cost_price (optional)',
                'wholesale_price (optional)',
                'stock_quantity (default: 0)',
                'minimum_stock_level (default: 0)',
                'maximum_stock_level (optional)',
                'unit (e.g., piece, kg, liter)',
                'description (optional)',
                'brand (optional)',
                'supplier (optional)',
                'tax_rate (e.g., 7.5 for 7.5%)',
                'track_stock (yes/no - default: yes)',
                'is_active (yes/no - default: yes)',
                'is_featured (yes/no - default: no)'
            ]);
            
            // Sample data rows
            fputcsv($file, [
                'Rice 50kg Premium',
                'PRD-RICE50',
                '1234567890123',
                'Groceries',
                '35000',
                '30000',
                '33000',
                '50',
                '10',
                '100',
                'bag',
                'Premium quality rice, 50kg bag',
                'Golden Harvest',
                'ABC Supplies Ltd',
                '7.5',
                'yes',
                'yes',
                'no'
            ]);
            
            fputcsv($file, [
                'Coca Cola 35cl',
                '',
                '2345678901234',
                'Beverages',
                '200',
                '150',
                '180',
                '200',
                '50',
                '500',
                'bottle',
                'Refreshing soft drink',
                'Coca Cola',
                'Coca Cola Distributor',
                '7.5',
                'yes',
                'yes',
                'yes'
            ]);
            
            fputcsv($file, [
                'Hand Sanitizer 500ml',
                'PRD-SANITIZER',
                '',
                'Personal Care',
                '1500',
                '1000',
                '',
                '30',
                '5',
                '50',
                'bottle',
                'Antibacterial hand sanitizer with Aloe Vera',
                'SafeHands',
                'Health Products Inc',
                '0',
                'yes',
                'yes',
                'no'
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Import products from CSV
     */
    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
            'skip_duplicates' => 'in:on,NULL',
            'update_existing' => 'in:on,NULL',
        ]);
        
        DB::beginTransaction();
        
        try {
            $file = $request->file('csv_file');
            $skipDuplicates = $request->has('skip_duplicates');
            $updateExisting = $request->has('update_existing');
            
            // Read CSV file
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            
            // Remove header row
            $headers = array_shift($csvData);
            
            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];
            
            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and index starts at 0
                
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }
                    
                    // Map CSV columns to array
                    $data = [
                        'name' => trim($row[0] ?? ''),
                        'sku' => trim($row[1] ?? ''),
                        'barcode' => trim($row[2] ?? ''),
                        'category_name' => trim($row[3] ?? ''),
                        'price' => trim($row[4] ?? '0'),
                        'cost_price' => trim($row[5] ?? null),
                        'wholesale_price' => trim($row[6] ?? null),
                        'stock_quantity' => trim($row[7] ?? '0'),
                        'minimum_stock_level' => trim($row[8] ?? '0'),
                        'maximum_stock_level' => trim($row[9] ?? null),
                        'unit' => trim($row[10] ?? 'piece'),
                        'description' => trim($row[11] ?? ''),
                        'brand' => trim($row[12] ?? ''),
                        'supplier' => trim($row[13] ?? ''),
                        'tax_rate' => trim($row[14] ?? '0'),
                        'track_stock' => strtolower(trim($row[15] ?? 'yes')) === 'yes',
                        'is_active' => strtolower(trim($row[16] ?? 'yes')) === 'yes',
                        'is_featured' => strtolower(trim($row[17] ?? 'no')) === 'yes',
                    ];
                    
                    // Validate required fields
                    if (empty($data['name'])) {
                        $errors[] = "Row {$rowNumber}: Product name is required";
                        $skipped++;
                        continue;
                    }
                    
                    if (empty($data['category_name'])) {
                        $errors[] = "Row {$rowNumber}: Category is required";
                        $skipped++;
                        continue;
                    }
                    
                    // Find or create category
                    $category = Category::where('name', $data['category_name'])->first();
                    
                    if (!$category) {
                        $category = Category::create([
                            'name' => $data['category_name'],
                            'is_active' => true
                        ]);
                    }
                    
                    $data['category_id'] = $category->id;
                    unset($data['category_name']);
                    
                    // Handle empty string values
                    if ($data['sku'] === '') {
                        $data['sku'] = 'PRD-' . strtoupper(Str::random(8));
                    }
                    if ($data['barcode'] === '') {
                        $data['barcode'] = null;
                    }
                    if ($data['description'] === '') {
                        $data['description'] = null;
                    }
                    if ($data['brand'] === '') {
                        $data['brand'] = null;
                    }
                    if ($data['supplier'] === '') {
                        $data['supplier'] = null;
                    }
                    if ($data['cost_price'] === '') {
                        $data['cost_price'] = null;
                    }
                    if ($data['wholesale_price'] === '') {
                        $data['wholesale_price'] = null;
                    }
                    if ($data['maximum_stock_level'] === '') {
                        $data['maximum_stock_level'] = null;
                    }
                    
                    // Check for existing product by SKU or name
                    $existingProduct = null;
                    if (!empty($data['sku'])) {
                        $existingProduct = Product::where('sku', $data['sku'])->first();
                    }
                    
                    if (!$existingProduct) {
                        $existingProduct = Product::where('name', $data['name'])
                            ->where('category_id', $data['category_id'])
                            ->first();
                    }
                    
                    if ($existingProduct) {
                        if ($updateExisting) {
                            // Update existing product
                            $previousStock = $existingProduct->stock_quantity;
                            $existingProduct->update($data);
                            
                            // Log stock change if quantity changed
                            if ($previousStock != $data['stock_quantity']) {
                                InventoryLog::create([
                                    'product_id' => $existingProduct->id,
                                    'user_id' => Auth::guard('user')->id(),
                                    'action_type' => 'adjustment',
                                    'quantity_change' => $data['stock_quantity'] - $previousStock,
                                    'previous_stock' => $previousStock,
                                    'new_stock' => $data['stock_quantity'],
                                    'reason' => 'CSV bulk import update',
                                    'action_date' => now(),
                                ]);
                            }
                            
                            $updated++;
                        } else if ($skipDuplicates) {
                            $skipped++;
                            continue;
                        } else {
                            $errors[] = "Row {$rowNumber}: Product '{$data['name']}' already exists (SKU: {$existingProduct->sku})";
                            $skipped++;
                            continue;
                        }
                    } else {
                        // Create new product
                        $product = Product::create($data);
                        
                        // Log initial inventory
                        if ($product->stock_quantity > 0) {
                            InventoryLog::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::guard('user')->id(),
                                'action_type' => 'purchase',
                                'quantity_change' => $product->stock_quantity,
                                'previous_stock' => 0,
                                'new_stock' => $product->stock_quantity,
                                'reason' => 'CSV bulk import - initial stock',
                                'action_date' => now(),
                            ]);
                        }
                        
                        $imported++;
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    $skipped++;
                }
            }
            
            DB::commit();
            
            $message = "Import completed: {$imported} products imported, {$updated} updated, {$skipped} skipped.";
            
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " and " . (count($errors) - 5) . " more errors.";
                }
            }
            
            return redirect()->route('lounge.products.bulk-upload')
                ->with('success', $message)
                ->with('import_stats', [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors
                ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}