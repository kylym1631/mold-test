<?php

namespace App\Imports;

use App\Models\Candidate;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class CandidatesImport implements ToModel
{
    protected $index = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->index++;

        $item = [
            'lastName' => $row[0],
            'firstName' => $row[1],
            'phone' => '+00000000'. $this->index,
            'viber' => '+00000000'. $this->index,
            'dateOfBirth' => '2000-01-01',
            // 'date_start_work' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]),
            'client_id' => $row[2],
            'recruiter_id' => 113,
            'active' => 8,
        ];

        dump($item);

        return new Candidate($item);
    }
}
