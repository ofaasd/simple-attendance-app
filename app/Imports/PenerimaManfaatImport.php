<?php

namespace App\Imports;

use App\Models\PenerimaManfaat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PenerimaManfaatImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Assuming row 1 is header
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!isset($row[0])) {
            return null;
        }

        return new PenerimaManfaat([
            'nama'     => $row[0],
            'kategori' => isset($row[1]) ? $row[1] : null,
            'alamat'   => isset($row[2]) ? $row[2] : null,
            'no_telp'  => isset($row[3]) ? $row[3] : null,
            'pic'      => isset($row[4]) ? $row[4] : null,
        ]);
    }
}
