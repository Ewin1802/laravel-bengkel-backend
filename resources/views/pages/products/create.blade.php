@extends('layouts.app')

@section('title', 'Tambah Produk')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Produk</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label>Barang Masuk</label>
                                <select name="product_in_id" id="product_in_id" class="form-control select2">
                                    <option value="">Pilih Barang</option>
                                    @foreach ($productIns as $productIn)
                                        <option value="{{ $productIn->id }}"
                                            data-name="{{ $productIn->productName->name ?? 'Tidak ada nama' }}"
                                            data-unit="{{ $productIn->productName->unit ?? '-' }}"
                                            data-amount="{{ $productIn->amount }}" {{-- Total harga beli --}}
                                            data-quantity="{{ $productIn->quantity }}"> {{-- Jumlah barang --}}
                                            {{ $productIn->productName->name ?? 'Tidak ada nama' }} |
                                            Rp {{ number_format($productIn->amount, 0, ',', '.') }} |
                                            Jumlah barang: {{ $productIn->quantity }} {{ $productIn->productName->unit ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_in_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" id="category_id" class="form-control select2">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Harga Satuan (Rp)</label>
                                <input type="text" id="unit_price" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Stok Produk</label>
                                <input type="text" id="stock" class="form-control" readonly value="0">
                                <small id="stock-loading" style="display: none; color: red;">Sedang Memeriksa Stok...</small>
                            </div>

                            <div class="form-group">
                                <label>Harga Jual (Rp)</label>
                                <div class="input-group">
                                    <input type="text" id="price_display" class="form-control" name="price_display">
                                    <input type="hidden" id="price_input" name="price">
                                    @error('price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <div class="input-group-append">
                                        <button type="button" id="edit_price_btn" class="btn btn-warning" style="display: none;">Ubah Harga</button>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>Gambar Produk</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('library/select2/dist/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        $('#productForm').on('submit', function (e) {
        let errors = [];

        if ($('#category_id').val() === '') {
            errors.push("Kategori harus dipilih.");
        }

        if ($('#product_in_id').val() === '') {
            errors.push("Barang masuk harus dipilih.");
        }

        if ($('#price_input').val() === '') {
            errors.push("Harga jual tidak boleh kosong.");
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert("Harap periksa kembali:\n" + errors.join("\n"));
        }
    });

            $('#product_in_id').select2();

            // Saat barang masuk dipilih
            $('#product_in_id').on('change', function () {
            let selectedOption = $(this).find(':selected');
            let productName = selectedOption.data('name');
            let unit = selectedOption.data('unit');
            let totalAmount = parseFloat(selectedOption.data('amount')) || 0; // Total harga beli
            let quantity = parseFloat(selectedOption.data('quantity')) || 1; // Jumlah barang
            let unitPrice = (quantity > 0) ? totalAmount / quantity : 0; // Harga satuan

            console.log("Barang dipilih:", productName);
            console.log("Satuan:", unit);
            console.log("Total Harga:", totalAmount);
            console.log("Jumlah Barang:", quantity);
            console.log("Harga Satuan:", unitPrice);

            // Tampilkan harga satuan di input
            $('#unit_price').val(new Intl.NumberFormat('id-ID').format(unitPrice));

            // Cek stok hanya jika name & unit valid
            if (productName && unit) {
                $('#stock-loading').show();
                checkStock(productName, unit);
            }
        });

        // Fungsi AJAX untuk cek stok & harga jual
        function checkStock(name, unit) {
            $.ajax({
                url: "{{ route('products.checkStock') }}",
                type: "GET",
                data: { name: name, unit: unit },
                beforeSend: function () {
                    $('#stock-loading').show();
                },
                success: function (response) {
                    console.log("Response dari server:", response);
                    $('#stock').val(response.stock);

                    if (response.price !== null) {
                        // Jika produk sudah ada, tampilkan harga jual sebelumnya dan kunci input
                        let formattedPrice = new Intl.NumberFormat('id-ID').format(response.price);
                        $('#price_display').val(formattedPrice).prop('readonly', true);
                        $('#price_input').val(response.price);
                        $('#edit_price_btn').show();
                    } else {
                        // Jika produk baru, kosongkan harga dan buat input bisa diedit
                        $('#price_display').val('').prop('readonly', false);
                        $('#price_input').val('');
                        $('#edit_price_btn').hide(); // Sembunyikan tombol ubah harga
                    }

                    // Pilih kategori sebelumnya jika ada
                    if (response.category_id !== null) {
                        $('#category_id').val(response.category_id).trigger('change');
                    } else {
                        $('#category_id').val('').trigger('change');
                    }
                },
                complete: function () {
                    $('#stock-loading').hide();
                },
                error: function () {
                    $('#stock').val(0);
                    $('#price_display').val('').prop('readonly', false);
                    $('#price_input').val('');
                    $('#edit_price_btn').hide();
                    $('#category_id').val('').trigger('change');
                    $('#stock-loading').hide();
                }
            });
        }


        // Event: Klik tombol "Ubah Harga" untuk mengedit harga jual
        $('#edit_price_btn').on('click', function () {
            $('#price_display').prop('readonly', false).focus();
        });

        // Simpan harga baru saat input diubah
        $('#price_display').on('input', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(value);
            this.value = formatted;
            $('#price_input').val(value); // Simpan nilai asli di input hidden
        });
    });



    // Format harga jual
    function formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
        document.getElementById('price_input').value = value;
    }

</script>
@endpush
