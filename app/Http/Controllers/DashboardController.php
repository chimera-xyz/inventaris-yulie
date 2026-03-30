<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $recentLogLimit = 3;

        $statusCounts = Item::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalItems = (int) $statusCounts->sum();
        $totalCategories = Category::count();
        $availableItems = (int) ($statusCounts['available'] ?? 0);
        $inUseItems = (int) ($statusCounts['in_use'] ?? 0);
        $brokenItems = (int) ($statusCounts['broken'] ?? 0);
        $maintenanceItems = (int) ($statusCounts['maintenance'] ?? 0);
        $lostItems = (int) ($statusCounts['lost'] ?? 0);
        $totalAssetValue = (float) Item::sum('price');
        $attentionItemsCount = $brokenItems + $maintenanceItems + $lostItems;
        $warrantyAlertCount = Item::where('has_warranty', true)
            ->whereNotNull('warranty_expiry')
            ->whereBetween('warranty_expiry', [today(), today()->addDays(45)])
            ->count();

        $recentItems = Item::with('category')
            ->latest()
            ->take(6)
            ->get();

        $recentLogs = ItemLog::with(['item.category', 'user'])
            ->latest()
            ->take($recentLogLimit)
            ->get();

        $hasMoreRecentLogs = ItemLog::count() > $recentLogLimit;

        $warrantyAlerts = Item::with('category')
            ->where('has_warranty', true)
            ->whereNotNull('warranty_expiry')
            ->whereBetween('warranty_expiry', [today(), today()->addDays(45)])
            ->orderBy('warranty_expiry')
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalItems',
            'totalCategories',
            'availableItems',
            'inUseItems',
            'brokenItems',
            'maintenanceItems',
            'lostItems',
            'attentionItemsCount',
            'totalAssetValue',
            'warrantyAlertCount',
            'recentItems',
            'recentLogs',
            'hasMoreRecentLogs',
            'warrantyAlerts'
        ));
    }
}
