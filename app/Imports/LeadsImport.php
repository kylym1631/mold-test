<?php

namespace App\Imports;

use App\Services\LeadsService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class LeadsImport implements ToArray
{
    public $exclude_first_row = false;
    public $columns = [];

    public function array($rows)
    {
        $ls = new LeadsService;
        $import_columns = $ls->import_columns;

        if ($this->exclude_first_row) {
            array_shift($rows);
        }

        $n_rows = [];

        foreach ($rows as $row) {
            $t_r = [];

            foreach ($this->columns as $c_key => $c_v) {
                $t_r[$import_columns[$c_key]] = $row[$c_v];
            }

            $n_rows[] = $t_r;
        }

        $ls->store(array_reverse($n_rows), true);
    }
}
