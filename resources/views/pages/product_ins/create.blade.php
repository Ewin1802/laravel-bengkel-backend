@extends('layouts.app')

@section('title', 'Tambah Barang Masuk')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Barang Masuk</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ route('product_ins.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <div class="input-group">
                                    <select id="product_name_id" class="form-control select2" name="product_name_id" required>
                                        <option value="">-- Pilih / Ketik Nama Barang --</option>
                                        @foreach($productNames as $productName)
                                            <option value="{{ $productName->id }}" data-unit="{{ $productName->unit }}">
                                                {{ $productName->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahBarang">
                                            <i class="fas fa-plus"></i> Tambah Barang
                                        </button>
                                    </div>
                                </div>
                                @error('product_name_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text" id="unit" class="form-control" name="unit" readonly>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Barang</label>
                                <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', 1) }}" min="1" oninput="calculateUnitPrice()">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Harga Total (Rp) *Cek di Nota</label>
                                <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror"
                                       placeholder="Masukkan Harga Total" oninput="formatPrice(this); calculateUnitPrice()">
                                <input type="hidden" id="amount_input" name="amount">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Harga Per Satuan Barang (Rp)</label>
                                <input type="text" id="unit_price" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="supplier_id" class="form-control select2 @error('supplier_id') is-invalid @enderror">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Penerima</label>
                                <input type="text" name="receiver" class="form-control @error('receiver') is-invalid @enderror"
                                       value="{{ old('receiver') }}">
                                @error('receiver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Upload Bukti Transaksi (Opsional)</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="is_received" value="0">
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

<!-- Modal Tambah Barang -->
<div class="modal fade" id="modalTambahBarang" tabindex="-1" role="dialog" aria-labelledby="modalTambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangLabel">Tambah Barang Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambahBarang">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="new_product_name" required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" class="form-control" id="new_unit" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('library/select2/dist/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#product_name_id').select2();

        $('#product_name_id').on('change', function () {
            let unit = $(this).find(':selected').data('unit') || '';
            $('#unit').val(unit);
        });

        $('#formTambahBarang').on('submit', function (e) {
            e.preventDefault();
            let name = $('#new_product_name').val().trim();
            let unit = $('#new_unit').val().trim();

            if (name === '' || unit === '') {
                alert("Nama dan satuan harus diisi!");
                return;
            }

            $.ajax({
                url: "{{ route('product_names.store') }}",
                type: "POST",
                data: {
                    name: name,
                    unit: unit
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    let newOption = new Option(response.name, response.id, true, true);
                    $('#product_name_id').append(newOption).trigger('change');
                    $('#unit').val(response.unit);
                    $('#modalTambahBarang').modal('hide');
                },
                error: function () {
                    alert("Gagal menyimpan barang!");
                }
            });
        });
    });

    function formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
        document.getElementById('amount_input').value = value;
    }

    function calculateUnitPrice() {
        let amount = document.getElementById('amount_input').value.replace(/\./g, '') || 0;
        let quantity = document.getElementById('quantity').value || 1;
        document.getElementById('unit_price').value = new Intl.NumberFormat('id-ID').format(amount / quantity);
    }
</script>
@endpush
