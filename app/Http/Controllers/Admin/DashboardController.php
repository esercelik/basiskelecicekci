<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $counts = [
            'activeProducts' => Product::query()->active()->count(),
            'inactiveProducts' => Product::query()->where('is_active', false)->count(),
            'outOfStockProducts' => Product::query()->where('stock_status', Product::STOCK_STATUS_OUT_OF_STOCK)->count(),
            'featuredProducts' => Product::query()->featured()->count(),
            'activeCategories' => Category::query()->active()->count(),
        ];
        $recentProducts = Product::query()
            ->with(['category:id,name', 'primaryImage:id,product_id,image_path,alt_text'])
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('counts', 'recentProducts'));
    }
}
