@extends('layouts.app')

@section('title', 'Edit Produk')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Produk</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="card-body">

                            <div class="form-group">
                                <label>Barang Masuk</label>
                                <select name="product_in_id" id="product_in_id" class="form-control select2" disabled>
                                    <option value="">Pilih Barang</option>
                                    @foreach ($productIns as $productIn)
                                        <option value="{{ $productIn->id }}" {{ $product->product_in_id == $productIn->id ? 'selected' : '' }}>
                                            {{ $productIn->productName->name ?? 'Tidak ada nama' }} |
                                            Rp {{ number_format($productIn->amount, 0, ',', '.') }} |
                                            Jumlah: {{ $productIn->quantity }} {{ $productIn->productName->unit ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="product_in_id" value="{{ $product->product_in_id }}">
                            </div>

                            <div class="form-group">
                                <label>Harga Satuan (Rp)</label>
                                <input type="text" class="form-control" id="unit_price" value="Rp {{ number_format($product->price, 0, ',', '.') }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Stok Produk</label>
                                <input type="text" class="form-control" id="stock" value="{{ $product->stock }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control select2">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Harga Jual (Rp)</label>
                                <input type="text" class="form-control" id="price_display" name="price" value="{{ number_format($product->price, 0, ',', '.') }}" required oninput="formatPrice(this)">
                                <input type="hidden" id="price_input" name="price" value="{{ $product->price }}">
                            </div>

                            <div class="form-group">
                                <label>Gambar Produk</label>
                                <div>
                                    @if ($product->image)
                                        <img src="{{ asset($product->image) }}" width="100">
                                    @else
                                        <p>Tidak ada gambar</p>
                                    @endif
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('library/select2/dist/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#product_in_id').select2();
    });

    function formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
        document.getElementById('price_input').value = value;
    }
</script>
@endpush

