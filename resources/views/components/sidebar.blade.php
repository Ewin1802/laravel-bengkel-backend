<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">MJM PARTS</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">MJM</a>
        </div>
        <ul class="sidebar-menu">


            <li class="nav-item dropdown {{ Request::is('users*', 'categories*', 'suppl*', 'discounts*', 'product_names*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-file-alt"></i>  <!-- Ubah ikon ke file-alt -->
                    <span>Data Umum</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('users.index') }}">Pengguna</a>
                    </li>
                    <li class="{{ Request::is('categories*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('categories.index') }}">Kategori</a>
                    </li>
                    <li class="{{ Request::is('suppl*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('suppliers.index') }}">Supplier</a>
                    </li>
                    <li class="{{ Request::is('discounts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('discounts.index') }}">Diskon</a>
                    </li>
                    <li class="{{ Request::is('product_names*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product_names.index') }}">Ubah Nama Produk</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ Request::is('product_i*', 'products*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-cogs"></i> <!-- Ubah ikon ke cogs -->
                    <span>Data Produk</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('product_i*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('product_ins.index') }}">Produk Masuk</a>
                    </li>
                    <li class="{{ Request::is('products*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('products.index') }}">Produk Jual</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ Request::is('orde*', 'laporan-keuangan*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-chart-bar"></i> <!-- Ubah ikon ke chart-bar -->
                    <span>Laporan</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item {{ Request::is('orde*') ? 'active' : '' }}">
                        <a href="{{ route('order_reports.index') }}" class="nav-link">
                            <i class="fas fa-receipt"></i> <span>Laporan Order</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('top*') ? 'active' : '' }}">
                        <a href="{{ route('top.products') }}" class="nav-link">
                            <i class="fas fa-box"></i> <span>Laporan Produk</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('laporan-keuangan*') ? 'active' : '' }}">
                        <a href="{{ route('financial.report') }}" class="nav-link">
                            <i class="fas fa-coins"></i> <span>Analisa Keuangan</span>
                        </a>
                    </li>
                </ul>
            </li>




</div>
