<?php

namespace App\Jobs;

use App\Models\ImportError;
use App\Models\Product; 
use Illuminate\Contracts\Queue\ShouldQueue; 
use Illuminate\Foundation\Queue\Queueable; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Validator;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3; 
    public array $backoff = [10, 60]; 
    public int $timeout = 60; 

    public function __construct(private string $csvPath) {} 

    public function handle(): void
    {
        $filePath = Storage::path($this->csvPath);

        if (!file_exists($filePath)) {
            Log::error("File CSV import tidak ditemukan: {$this->csvPath}");
            return;
        }

        ImportError::truncate();

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 
        $rows = array_map('str_getcsv', $lines); 
        $header = array_shift($rows);

        if (!$header) {
            ImportError::create([
                'row_number' => 1,
                'row_data' => null,
                'error_message' => 'File CSV kosong atau header tidak ditemukan.',
            ]);
            Log::warning('File CSV import kosong atau header tidak ditemukan.');
            Storage::delete($this->csvPath);
            return;
        }

        $rowNumber = 2; // Baris 1 adalah header, data dimulai dari baris 2
        $created = 0;
        $updated = 0;
        $failed = 0;

        foreach ($rows as $row) { 
            if (count($row) !== count($header)) { 
                $errorMsg = 'Jumlah kolom (' . count($row) . ') tidak cocok dengan header (' . count($header) . ')';
                ImportError::create([
                    'row_number' => $rowNumber,
                    'row_data' => implode(', ', $row),
                    'error_message' => $errorMsg,
                ]);
                Log::warning("Import produk gagal di baris {$rowNumber}: {$errorMsg}");
                $failed++;
                $rowNumber++;
                continue; 
            }

            $rowData = array_combine($header, $row);

            $validator = Validator::make($rowData, [
                'name' => ['required', 'string'],
                'category_id' => ['required', 'exists:categories,id'],
                'price' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) { 
                $errorMsg = implode(', ', $validator->errors()->all());
                ImportError::create([
                    'row_number' => $rowNumber,
                    'row_data' => json_encode($rowData, JSON_UNESCAPED_UNICODE),
                    'error_message' => $errorMsg,
                ]);
                Log::warning("Import produk gagal di baris {$rowNumber}: {$errorMsg}");
                $failed++;
                $rowNumber++;
                continue; 
            }

            $data = $validator->validated();
            $product = Product::updateOrCreate(
                ['name' => $data['name'], 'category_id' => $data['category_id']],
                ['price' => $data['price']]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;
            $rowNumber++;
        }   

        Log::info("Import produk selesai: {$created} baru, {$updated} diperbarui, {$failed} gagal.");
        Storage::delete($this->csvPath);
    }

    

    /**
     * Execute the job. // 
     */

}
