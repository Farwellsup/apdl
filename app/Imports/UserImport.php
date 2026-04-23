<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UserImport implements ToArray, WithHeadingRow, WithChunkReading
{
    public function array(array $rows)
    {
        return $rows;
    }

    public function chunkSize(): int
    {
        return 100;
    }

}