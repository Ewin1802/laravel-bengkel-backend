@extends('layouts.app')

@section('title', 'Produk Jual')
{{-- Favicon - Logo web di samping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Produk Jual</h1>
                <div class="section-header-button">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk Jual</a>
                </div>
            </div>

            <div class="section-body">

                {{-- <h2 class="section-title">Petunjuk</h2> --}}
                <p class="section-header">
                    Info : Daftar produk ini yang nanti akan muncul di Handphone Tablet Kasir. Harap memperhatikan gambar produk, harga jual, dan data lainnya pada saat menambah produk jual.
                </p>

                <div class="float-left">
                    <form method="GET" action="{{ route('products.index') }}">
                        <select class="form-control selectric" name="category_id" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="float-right">
                    <form method="GET" action="{{ route('products.index') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari Nama Produk" name="name" value="{{ request('name') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="clearfix mb-3"></div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Kategori</th>
                                    <th>Nama Produk</th>
                                    <th>Unit</th>
                                    <th>Harga Jual</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($product->image)
                                                <a href="#" data-toggle="modal" data-target="#imageModal" data-image="{{ asset($product->image) }}">
                                                    <img src="{{ asset($product->image) }}" width="50" height="50" class="img-thumbnail">
                                                </a>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->productName->unit ?? '-' }}</td>
                                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>
                                            <span class="badge {{ $product->status ? 'badge-success' : 'badge-warning' }}">
                                                {{ $product->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-info btn-sm">Edit</a>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="float-right">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal untuk menampilkan gambar ukuran penuh -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Gambar Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#imageModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Gambar yang diklik
            var imageSrc = button.data('image'); // Ambil URL gambar dari atribut data
            var modalImage = $('#modalImage'); // Temukan elemen gambar di dalam modal
            modalImage.attr('src', imageSrc); // Setel gambar di modal agar sesuai dengan gambar yang diklik
        });
    });
</script>
@endpush
