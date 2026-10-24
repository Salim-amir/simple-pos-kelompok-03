<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        Product::updateOrCreate(
            ['name' => $row['name'], 'category_id' => $row['category_id']],
            ['price' => $row['price']],
        );

        return null;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}