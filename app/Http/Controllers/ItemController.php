<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Support\ItemPdfExportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemController extends Controller
{
    private const ITEM_FIELDS = [
        'category_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'has_warranty',
        'warranty_expiry',
        'price',
        'location',
        'status',
        'notes',
        'specifications',
        'assigned_user_name',
        'assigned_division',
        'assigned_phone',
        'assigned_since',
    ];

    public function index(Request $request)
    {
        $filters = $request->only(['category', 'status', 'search']);

        $items = Item::query()
            ->with('category')
            ->filter($filters)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'filters'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedItemData($request);
        $itemData = $this->extractItemFields($validated);
        $uploadedPhotos = $this->uploadedPhotos($request);

        $item = DB::transaction(function () use ($itemData, $uploadedPhotos) {
            $category = Category::findOrFail($itemData['category_id']);
            $itemData['unique_code'] = $this->generateUniqueCode($category);

            $item = Item::create($itemData);
            $item->generateQRCode();
            $this->storePhotos($item, $uploadedPhotos);
            $item->log('qr_generated', notes: 'QR code digenerate untuk item ' . $item->unique_code);

            return $item->fresh(['category', 'photos']);
        });

        return redirect()->route('items.show', $item->unique_code)
            ->with('success', 'Item berhasil ditambahkan dengan kode: ' . $item->unique_code)
            ->with('asset_created_modal', $item->unique_code);
    }

    public function show(Item $item)
    {
        $item->load(['category', 'logs.user', 'photos', 'histories.creator']);
        $item->ensureQrCodeIsAvailable();

        $editingHistory = filled(request('edit_history'))
            ? $item->histories->firstWhere('id', (int) request('edit_history'))
            : null;

        return view('items.show', compact('item', 'editingHistory'));
    }

    public function edit(Item $item)
    {
        $item->load('photos');
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $this->validatedItemData($request, $item);
        $itemData = $this->extractItemFields($validated);
        $uploadedPhotos = $this->uploadedPhotos($request);
        $originalCategoryId = $item->category_id;

        DB::transaction(function () use ($itemData, $item, $originalCategoryId, $uploadedPhotos) {
            $item->update($itemData);

            if ((int) $originalCategoryId !== (int) $item->category_id) {
                $oldCategory = Category::find($originalCategoryId);
                $newCategory = Category::findOrFail($item->category_id);

                $item->update([
                    'unique_code' => $this->generateUniqueCode($newCategory, $item),
                ]);

                $item->generateQRCode();
                $item->log(
                    'qr_generated',
                    notes: "QR code diregenerate dari {$oldCategory?->code} ke {$newCategory->code}"
                );
            }

            $this->storePhotos($item, $uploadedPhotos);
        });

        return redirect()->route('items.show', $item->unique_code)
            ->with('success', 'Item berhasil diperbarui');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->activeLoan()->exists()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Item tidak dapat dihapus saat masih memiliki peminjaman aktif.');
        }

        $uniqueCode = $item->unique_code;
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item ' . $uniqueCode . ' berhasil dihapus');
    }

    public function generateQRCode(Item $item)
    {
        $item->generateQRCode();
        $item->log('qr_generated', notes: 'QR code digenerate manual untuk item ' . $item->unique_code);

        return redirect()->route('items.show', $item->unique_code)
            ->with('success', 'QR code berhasil digenerate');
    }

    public function printQRCode(Item $item)
    {
        $item->ensureQrCodeIsAvailable();

        $item->log('qr_printed', notes: 'QR code dicetak untuk item ' . $item->unique_code);

        return view('items.print-qr', compact('item'));
    }

    public function printCode(Item $item)
    {
        $item->log('code_printed', notes: 'Kode unik dicetak untuk item ' . $item->unique_code);

        return view('items.print-code', compact('item'));
    }

    public function printLabel(Item $item)
    {
        $item->ensureQrCodeIsAvailable();

        $item->log('qr_printed', notes: 'Label item dicetak untuk item ' . $item->unique_code);

        return view('items.print-label', compact('item'));
    }

    public function printBulkQRCode(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        $items = Item::whereIn('id', $validated['item_ids'])
            ->with('category')
            ->get();

        foreach ($items as $item) {
            $item->ensureQrCodeIsAvailable();
            $item->log('qr_printed', notes: 'QR code dicetak untuk item ' . $item->unique_code);
        }

        return view('items.print-bulk-qr', compact('items'));
    }

    public function destroyPhotos(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'photo_ids' => 'required|array|min:1',
            'photo_ids.*' => [
                'integer',
                Rule::exists('item_photos', 'id')->where(
                    fn ($query) => $query->where('item_id', $item->id)
                ),
            ],
        ]);

        $this->removePhotos($item, $validated['photo_ids']);

        return redirect()
            ->route('items.edit', $item)
            ->with('success', 'Foto terpilih berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $fileName = 'inventaris-it-' . now()->format('Y-m-d') . '.xlsx';
        $filters = $request->only(['category', 'status', 'search']);

        return Excel::download(new ItemsExport($filters), $fileName);
    }

    public function exportSelected(Request $request, ItemPdfExportService $pdfExportService): BinaryFileResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,pdf',
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:items,id',
        ]);

        $itemIds = array_values(array_unique(array_map('intval', $validated['item_ids'])));

        if ($validated['format'] === 'excel') {
            $fileName = 'inventaris-it-selected-' . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new ItemsExport([], $itemIds), $fileName);
        }

        $items = Item::query()
            ->with(['category', 'photos', 'histories'])
            ->whereIn('id', $itemIds)
            ->orderBy('unique_code')
            ->get();

        if ($items->count() === 1) {
            $item = $items->firstOrFail();
            $pdfPath = $pdfExportService->exportSingle($item);
            $downloadName = 'inventaris-it-' . Str::slug($item->unique_code, '-') . '-' . now()->format('Y-m-d') . '.pdf';

            return response()->download($pdfPath, $downloadName)->deleteFileAfterSend(true);
        }

        $zipPath = $pdfExportService->export($items);
        $downloadName = 'inventaris-it-pdf-' . now()->format('Y-m-d') . '.zip';

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }

    public function logs(Item $item)
    {
        $logs = $item->logs()->paginate(20);

        return view('items.logs', compact('item', 'logs'));
    }

    private function validatedItemData(Request $request, ?Item $item = null): array
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'has_warranty' => 'sometimes|boolean',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'price' => 'nullable|numeric',
            'location' => 'nullable|string|max:200',
            'status' => 'required|in:available,in_use,broken,maintenance,lost',
            'notes' => 'nullable|string',
            'specifications' => 'nullable|string',
            'assigned_user_name' => 'nullable|string|max:150',
            'assigned_division' => 'nullable|string|max:150',
            'assigned_phone' => 'nullable|string|max:30',
            'assigned_since' => 'nullable|date',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:15360',
        ];

        return $request->validate($rules, [
            'photos.*.uploaded' => 'Foto gagal diupload. Biasanya karena ukuran file dari HP terlalu besar. Coba pilih ulang, atau pakai foto yang lebih ringan.',
            'photos.*.image' => 'Foto asset harus berupa file gambar yang valid.',
            'photos.*.max' => 'Ukuran tiap foto maksimal 15 MB.',
        ]);
    }

    private function extractItemFields(array $validated): array
    {
        $itemData = [];

        foreach (self::ITEM_FIELDS as $field) {
            if (array_key_exists($field, $validated)) {
                $itemData[$field] = $validated[$field];
            }
        }

        if (array_key_exists('has_warranty', $itemData)) {
            $itemData['has_warranty'] = filter_var($itemData['has_warranty'], FILTER_VALIDATE_BOOLEAN);
        }

        if (($itemData['has_warranty'] ?? null) === false) {
            $itemData['warranty_expiry'] = null;
        }

        return $itemData;
    }

    private function generateUniqueCode(Category $category, ?Item $ignoreItem = null): string
    {
        $year = $this->resolveCodeYear($ignoreItem);
        $prefix = sprintf('%s-', $category->code);
        $suffix = sprintf('-%d', $year);
        $counter = Item::withTrashed()
            ->where('category_id', $category->getKey())
            ->where('unique_code', 'like', $prefix . '%' . $suffix)
            ->when($ignoreItem, fn ($query) => $query->whereKeyNot($ignoreItem->getKey()))
            ->pluck('unique_code')
            ->map(function (string $code) use ($suffix) {
                $codeWithoutSuffix = str_replace($suffix, '', $code);
                return (int) Str::afterLast($codeWithoutSuffix, '-');
            })
            ->max() ?? 0;

        $counter++;

        return sprintf('%s-%04d-%d', $category->code, $counter, $year);
    }

    private function resolveCodeYear(?Item $item = null): int
    {
        return (int) ($item?->created_at?->format('Y') ?? now()->format('Y'));
    }

    /**
     * @return UploadedFile[]
     */
    private function uploadedPhotos(Request $request): array
    {
        return array_values(array_filter(
            $request->file('photos', []),
            fn ($file) => $file instanceof UploadedFile
        ));
    }

    /**
     * @param UploadedFile[] $photos
     */
    private function storePhotos(Item $item, array $photos): void
    {
        if ($photos === []) {
            return;
        }

        $startingSortOrder = (int) ($item->photos()->max('sort_order') ?? 0);

        foreach ($photos as $index => $photo) {
            $path = $photo->store("item-photos/{$item->id}", 'public');

            $item->photos()->create([
                'path' => $path,
                'sort_order' => $startingSortOrder + $index + 1,
            ]);
        }

        $item->log('update', notes: count($photos) . ' foto asset ditambahkan');
    }

    private function removePhotos(Item $item, array $photoIds): void
    {
        if ($photoIds === []) {
            return;
        }

        $photos = $item->photos()->whereIn('id', $photoIds)->get();
        $count = $photos->count();

        foreach ($photos as $photo) {
            $photo->delete();
        }

        if ($count > 0) {
            $item->log('update', notes: $count . ' foto asset dihapus');
        }
    }
}
