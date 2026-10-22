<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Simple POS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('pos.create') }}">Kasir</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('transactions.index') }}">Transaksi</a>
                </li>
                @if (auth()->user()?->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('products.index') }}">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('categories.index') }}">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('reports.index') }}">Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('import.create') }}">Import</a>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center text-white gap-3">
                <span>{{ auth()->user()?->name }} ({{ auth()->user()?->role }})</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>