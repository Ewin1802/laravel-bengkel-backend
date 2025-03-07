@extends('layouts.app')

@section('title', 'Daftar Nama Produk')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Nama Produk</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                                @foreach ($productNames as $productName)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $productName->name }}</td>
                                        <td>{{ $productName->unit }}</td>
                                        <td>
                                            <a href="{{ route('product_names.edit', $productName->id) }}" class="btn btn-info btn-sm">Edit</a>
                                            {{-- <form action="{{ route('product_names.destroy', $productName->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus ini?')">Hapus</button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="float-right">
                            {{ $productNames->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
