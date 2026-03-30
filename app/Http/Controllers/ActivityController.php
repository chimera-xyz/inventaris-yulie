<?php

namespace App\Http\Controllers;

use App\Models\ItemLog;

class ActivityController extends Controller
{
    public function index()
    {
        $logs = ItemLog::query()
            ->with(['item.category', 'user'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('activities.index', compact('logs'));
    }
}
