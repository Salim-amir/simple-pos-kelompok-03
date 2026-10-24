@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border rounded-lg p-5 max-w-xl mb-6 shadow-sm">
        <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data">
            @csrf
            <label class="block mb-3">
                <span class="text-sm font-medium text-slate-700">Pilih File CSV</span>
                <input type="file" name="csv" accept=".csv" class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border rounded-md p-1">
                @error('csv')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </label>

            <p class="text-xs text-slate-500 mb-4">Kolom yang dibutuhkan pada CSV: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800">name</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800">category_id</code>, <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-800">price</code>.</p>

            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium">Mulai Import</button>
        </form>
    </div>

    {{-- Tabel Baris yang Gagal Di-import --}}
    <div class="bg-white border rounded-lg p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Daftar Baris Gagal Di-import</h2>
                <p class="text-xs text-slate-500">Mencatat nomor baris dan rincian pesan kesalahan saat proses import.</p>
            </div>
            @if (isset($importErrors) && $importErrors->isNotEmpty())
                <div class="flex items-center gap-3">
                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">
                        {{ $importErrors->count() }} baris gagal
                    </span>
                    <form method="POST" action="{{ route('import.errors.clear') }}" onsubmit="return confirm('Yakin ingin membersihkan semua riwayat error?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">
                            Bersihkan Riwayat
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if (isset($importErrors) && $importErrors->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                            <th class="py-2.5 px-3 w-24">Baris #</th>
                            <th class="py-2.5 px-3">Pesan Kesalahan</th>
                            <th class="py-2.5 px-3">Data Baris (CSV)</th>
                            <th class="py-2.5 px-3 w-40 text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($importErrors as $error)
                            <tr class="hover:bg-red-50/40">
                                <td class="py-2.5 px-3 font-semibold text-red-600">
                                    <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded">
                                        Baris {{ $error->row_number }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-red-700 text-xs">
                                    {{ $error->error_message }}
                                </td>
                                <td class="py-2.5 px-3 text-xs font-mono text-slate-600 max-w-xs break-all">
                                    {{ $error->row_data ?? '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-xs text-slate-400 text-right">
                                    {{ $error->created_at ? $error->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-400 text-sm">
                <p>Tidak ada riwayat baris error import.</p>
                <p class="text-xs text-slate-400 mt-1">Semua baris dari import sebelumnya berhasil diproses atau belum ada proses import yang dijalankan.</p>
            </div>
        @endif
    </div>
    <div style="margin-top:24px;border:1px solid #cbd5e1;border-radius:8px;padding:20px;max-width:576px;">
    <h2 style="font-size:16px;font-weight:600;margin-bottom:12px;">Import dengan Laravel Excel (CSV atau XLSX)</h2>

    <form method="POST" action="{{ route('import.excel') }}" enctype="multipart/form-data">
        @csrf
        <label style="display:block;margin-bottom:12px;">
            <span style="display:block;font-size:14px;font-weight:500;margin-bottom:4px;">File CSV / XLSX</span>
            <input type="file" name="file" accept=".csv,.xlsx"
                   style="display:block;width:100%;border:1px solid #94a3b8;border-radius:6px;padding:8px;font-size:14px;">
            @error('file')
                <p style="color:#dc2626;font-size:14px;margin-top:4px;">{{ $message }}</p>
            @enderror
        </label>
        <button type="submit"
                style="background:#0f172a;color:#fff;padding:8px 16px;border-radius:6px;font-size:14px;border:0;cursor:pointer;">
            Import (Laravel Excel)
        </button>
    </form>
</div>
@endsection