<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Imports\ProductsImport;
use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\ImportError;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');

        $query = $this->filteredQuery($from, $to, $categoryId);

        return view('reports.index', [
            'totalPenjualan'  => $query->sum('total'),
            'jumlahTransaksi' => $query->count(),
            'from'            => $from->toDateString(),
            'to'              => $to->toDateString(),
            'categories'      => Category::all(),
            'categoryId'      => $categoryId,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');
        $query = $this->filteredQuery($from, $to, $categoryId);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total']);

            foreach ($query->lazyById() as $t) {
                fputcsv($out, [$t->id, $t->created_at, $t->user->name, $t->total]);
            }

            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');

        return Excel::download(
            new SalesReportExport($this->filteredQuery($from, $to, $categoryId)),
            'laporan-penjualan.xlsx'
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolveDateRange($request);
        $categoryId = $request->input('category_id');

        $topProducts = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$from, $to])
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('products.category_id', $categoryId);
            })
            ->select('products.name', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'totalPenjualan'  => $this->filteredQuery($from, $to, $categoryId)->sum('total'),
            'jumlahTransaksi' => $this->filteredQuery($from, $to, $categoryId)->count(),
            'transactions'    => $this->filteredQuery($from, $to, $categoryId)->latest()->limit(50)->get(),
            'topProducts'     => $topProducts,
            'from'            => $from->toDateString(),
            'to'              => $to->toDateString(),
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function importForm(): View
    {
        $importErrors = ImportError::orderBy('row_number', 'asc')->get();

        return view('reports.import', [
            'importErrors' => $importErrors,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('csv')->store('imports');

        ProcessProductImport::dispatch($path);

        return back()->with('success', 'Import sedang diproses di background.');
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return back()->with('success', 'Import selesai.');
    }

    public function clearImportErrors(): RedirectResponse
    {
        ImportError::truncate();

        return back()->with('success', 'Riwayat error import berhasil dibersihkan.');
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to   = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        return [$from, $to];
    }

    private function filteredQuery($from, $to, $categoryId = null): Builder
    {
        return Transaction::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->when($categoryId, function ($query, $categoryId) {
                $query->whereHas('details.product', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            });
    }
}