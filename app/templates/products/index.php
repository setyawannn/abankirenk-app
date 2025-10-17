@extends('layouts.app')

@section('title')
Daftar Produk
@endsection

@section('content')
<h1>{{ $title }}</h1>

@if (empty($products))
<p>Tidak ada produk yang tersedia.</p>
@else
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product['id'] }}</td>
            <td>{{ $product['nama_lengkap'] }}</td>
            <td>Rp {{ number_format($product['price']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection