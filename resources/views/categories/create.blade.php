@extends('layouts.app')

@section('title', 'Tambah Kategori - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', 'Tambah Kategori')
@section('page-subtitle', 'Tambah kategori baru untuk pengelompokan asset.')

@section('content')
    @include('categories._form', [
        'heading' => 'Definisi Kategori',
        'description' => 'Atur kode, nama, dan catatan kategori sesuai kebutuhan inventaris Anda.',
        'action' => route('categories.store'),
        'submitLabel' => 'Simpan Kategori',
    ])
@endsection
