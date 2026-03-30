@extends('layouts.app')

@section('title', 'Tambah Asset - Inventaris IT')
@section('page-eyebrow', '')
@section('page-title', 'Tambah Asset Baru')
@section('page-subtitle', 'Masukkan data asset baru.')

@section('content')
    @include('items._form', [
        'heading' => 'Data Asset',
        'description' => 'Input identitas, klasifikasi, pengguna saat ini, spesifikasi lengkap, dan catatan internal asset.',
        'action' => route('items.store'),
        'submitLabel' => 'Simpan Asset',
        'cancelUrl' => route('items.index'),
    ])
@endsection
