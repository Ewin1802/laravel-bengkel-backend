@extends('layouts.app')

@section('title', 'Edit Barang Masuk')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Barang Masuk</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Barang Masuk</a></div>
                    <div class="breadcrumb-item">Edit Barang Masuk</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Form Edit Barang Masuk</h2>

                <div class="card">
                    <form action="{{ route('product_ins.update', $productIn->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <select name="product_name_id"
                                    class="form-control select2 @error('product_name_id') is-invalid @enderror" id="product_name_id">
                                    <option value="" disabled>Pilih Nama Barang</option>
                                    @foreach ($productNames as $name)
                                        <option value="{{ $name->id }}"
                                            data-unit="{{ $name->unit }}"
                                            {{ $productIn->product_name_id == $name->id ? 'selected' : '' }}>
                                            {{ $name->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_name_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text" id="unit" class="form-control" name="unit" value="{{ $productIn->productName->unit ?? '' }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Jumlah Barang</label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', $productIn->quantity) }}" min="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Harga Total (Rp) *Cek di Nota</label>
                                <input type="text" id="amount_display"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ number_format($productIn->amount, 0, ',', '.') }}"
                                       oninput="formatPrice(this)">
                                <input type="hidden" id="amount_input" name="amount" value="{{ $productIn->amount }}">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="supplier_id" class="form-control select2 @error('supplier_id') is-invalid @enderror">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $productIn->supplier_id == $supplier->id ? 'selected' : '' }}>
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
                                <input type="text" name="receiver"
                                       class="form-control @error('receiver') is-invalid @enderror"
                                       value="{{ old('receiver', $productIn->receiver) }}">
                                @error('receiver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- <div class="form-group">
                                <label>Status Penerimaan</label>
                                <select name="is_received" class="form-control @error('is_received') is-invalid @enderror">
                                    <option value="1" {{ $productIn->is_received == 1 ? 'selected' : '' }}>Sudah Diterima</option>
                                    <option value="0" {{ $productIn->is_received == 0 ? 'selected' : '' }}>Belum Diterima</option>
                                </select>
                                @error('is_received')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description', $productIn->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Gambar Bukti (Opsional)</label>
                                @if ($productIn->image)
                                    <img src="{{ asset($productIn->image) }}" class="img-thumbnail mb-2" width="100">
                                @endif
                                <input type="file" name="image" id="imageInput"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <img id="imagePreview" src="" class="mt-2" width="100" style="display: none;">
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#product_name_id').on('change', function() {
                let unit = $(this).find(':selected').data('unit') || '';
                $('#unit').val(unit);
            });
        });

        function formatPrice(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(value);
            input.value = formatted;
            document.getElementById('amount_input').value = value;
        }

        document.getElementById('imageInput').addEventListener('change', function(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let preview = document.getElementById('imagePreview');
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
@endpush
