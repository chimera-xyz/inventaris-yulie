@extends('layouts.app')

@section('title', 'Edit Asset - Inventaris IT')
@section('page-eyebrow', 'Asset Registry')
@section('page-title', 'Edit Asset')
@section('page-subtitle', 'Perbarui informasi asset dan catat perubahan yang diperlukan.')

@section('page-actions')
    <a href="{{ route('items.show', $item) }}" class="btn btn--secondary">Kembali ke Detail</a>
@endsection

@section('content')
    @include('items._form', [
        'item' => $item,
        'heading' => 'Perbarui Data Asset',
        'description' => "Perbarui data untuk {$item->unique_code}, termasuk pengguna saat ini, spesifikasi, dan catatan internal bila diperlukan.",
        'action' => route('items.update', $item),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'cancelUrl' => route('items.show', $item),
    ])
@endsection
