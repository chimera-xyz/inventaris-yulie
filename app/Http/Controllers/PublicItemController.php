<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;

class PublicItemController extends Controller
{
    public function show(Item $item): View
    {
        $item->load([
            'category',
            'photos',
            'histories',
        ]);

        return view('items.public-show', compact('item'));
    }
}
