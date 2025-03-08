@extends('layouts.app')

@section('title', 'Daftar Barang Masuk')
{{-- Favicon - Logo web disamping title --}}
<link rel="icon" href="{{ asset('img/logo_arch_web.png') }}" type="image/png">

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Barang Masuk</h1>
                <div class="section-header-button">
                    <a href="{{ route('product_ins.create') }}" class="btn btn-primary">Tambah Barang Masuk</a>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Semua Barang Masuk</h4>
                    </div>

                    <div class="card-body">
                        <div class="float-left">
                            <form method="GET" action="{{ route('product_ins.index') }}">
                                <select class="form-control selectric" name="is_received" onchange="this.form.submit()">
                                    <option value="" {{ request('is_received') === null ? 'selected' : '' }}>Semua</option>
                                    <option value="1" {{ request('is_received') == '1' ? 'selected' : '' }}>Sudah Di Etalase</option>
                                    <option value="0" {{ request('is_received') == '0' ? 'selected' : '' }}>Belum Di Etalase</option>
                                </select>
                            </form>
                        </div>

                        <div class="float-right">
                            <form method="GET" action="{{ route('product_ins.index') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari Nama Barang" name="product_name" value="{{ request('product_name') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Barang</th>
                                    <th>Harga Total Belanja</th>
                                    <th>Supplier</th>
                                    <th>Penerima</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>

                                @foreach ($productIns as $productIn)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($productIn->image)
                                                <a href="#" data-toggle="modal" data-target="#imageModal" data-image="{{ asset($productIn->image) }}">
                                                    <img src="{{ asset($productIn->image) }}" width="50" height="50" class="img-thumbnail">
                                                </a>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>{{ $productIn->productName->name ?? '-' }}</td>
                                        <td>{{ $productIn->quantity }}</td>
                                        <td>Rp {{ number_format($productIn->amount, 0, ',', '.') }}</td>
                                        <td>{{ $productIn->supplier->name ?? '-' }}</td>
                                        <td>{{ $productIn->receiver ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $productIn->is_received ? 'badge-success' : 'badge-warning' }}">
                                                {{ $productIn->is_received ? 'Sudah Di Etalase' : 'Belum Di Etalase' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('product_ins.edit', $productIn->id) }}" class="btn btn-info btn-sm">Edit</a>
                                            <form action="{{ route('product_ins.destroy', $productIn->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="float-right">
                            {{ $productIns->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal untuk menampilkan gambar penuh -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Bukti Transaksi (Nota)</h5>
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
