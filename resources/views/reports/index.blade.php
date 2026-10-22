@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="container my-4">
    <h2 class="mb-4">Laporan Penjualan</h2>

    <form action="{{ route('reports.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Dari</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Sampai</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Total Penjualan</h5>
            <h3 class="card-text text-success">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
            <p class="card-text text-muted">{{ $jumlahTransaksi }} transaksi</p>
        </div>
    </div>
</div>
@endsection