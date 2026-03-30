<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function __construct(
        protected array $filters = [],
        protected array $itemIds = [],
    ) {
    }

    public function collection()
    {
        return Item::query()
            ->with('category')
            ->when($this->itemIds !== [], fn ($query) => $query->whereIn('id', $this->itemIds))
            ->filter($this->filters)
            ->orderBy('unique_code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Unik',
            'Kategori',
            'Nama Item',
            'Merek',
            'Model',
            'Nomor Seri',
            'Tanggal Beli',
            'Status Garansi',
            'Harga',
            'Lokasi',
            'Status',
            'Pengguna',
            'Divisi',
            'Nomor Telepon',
            'Dipakai Sejak',
            'Spesifikasi Lengkap',
            'Catatan',
            'Dibuat Pada',
        ];
    }

    public function map($item): array
    {
        return [
            $item->unique_code,
            $item->category->name ?? '-',
            $item->name,
            $item->brand ?? '-',
            $item->model ?? '-',
            $item->serial_number ?? '-',
            $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-',
            $item->has_warranty
                ? ($item->warranty_expiry ? $item->warranty_expiry->format('d/m/Y') : 'Tanggal garansi belum dicatat')
                : 'Tidak ada garansi sejak pembelian',
            $item->price ? number_format($item->price, 2, ',', '.') : '-',
            $item->location ?? '-',
            $item->status,
            $item->assigned_user_name ?? '-',
            $item->assigned_division ?? '-',
            $item->assigned_phone ?? '-',
            $item->assigned_since ? $item->assigned_since->format('d/m/Y') : '-',
            $item->specifications ?? '-',
            $item->notes ?? '-',
            $item->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
}
