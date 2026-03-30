@extends('layouts.app')

@section('title', 'Edit Kategori - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', 'Edit Kategori')
@section('page-subtitle', 'Perbarui kategori.')

@section('page-actions')
    <a href="{{ route('categories.show', $category) }}" class="btn btn--secondary">Lihat Detail</a>
@endsection

@section('content')
    @include('categories._form', [
        'category' => $category,
        'heading' => 'Perbarui Kategori',
        'description' => 'Edit nama dan catatan kategori tanpa menghilangkan relasi terhadap asset yang sudah ada.',
        'action' => route('categories.update', $category),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
