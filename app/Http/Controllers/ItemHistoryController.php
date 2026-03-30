<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemHistoryController extends Controller
{
    public function store(Request $request, Item $item): RedirectResponse
    {
        $validated = $this->validatedData($request);

        $item->histories()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        $item->log('update', notes: 'Riwayat asset ditambahkan: ' . $validated['title']);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Riwayat asset berhasil ditambahkan.');
    }

    public function update(Request $request, Item $item, ItemHistory $history): RedirectResponse
    {
        abort_unless($history->item_id === $item->id, 404);

        $validated = $this->validatedData($request);
        $history->update($validated);

        $item->log('update', notes: 'Riwayat asset diperbarui: ' . $validated['title']);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'Riwayat asset berhasil diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'event_type' => 'required|in:maintenance,service,handover,relocation,incident,note',
            'event_date' => 'required|date',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'responsible_party' => 'nullable|string|max:150',
            'contact_phone' => 'nullable|string|max:30',
        ]);
    }
}
