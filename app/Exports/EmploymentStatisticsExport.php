<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class EmploymentStatisticsExport implements FromArray
{
    protected $statistics;
    protected $map;

    public function __construct(array $statistics)
    {
        $this->statistics = $statistics['data'];
        $this->statistics_sum = $statistics['sum_data'];
        $this->map = $statistics['table'];
    }

    public function array(): array
    {
        $result = [['Имя', 'Фамилия', 'Должность'] + $this->map];

        $sum_map = [
            'firstName' => 'Всего',
            'lastName' => '',
            'groupName' => '',
        ];

        $sum_map += $this->map;


        foreach ($this->statistics_sum['table'] as $key => $value) {
            if (isset($sum_map[$key])) {
                $sum_map[$key] = $value == 0 ? '0' : $value;
            }
        }

        $result[] = $sum_map;

        foreach ($this->statistics as $row) {
            $row_map = [
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'groupName' => $row['groupName'],
            ];

            $row_map += $this->map;

            foreach ($row['table'] as $key => $value) {
                if (isset($row_map[$key])) {
                    $row_map[$key] = $value == 0 ? '0' : $value;
                }
            }

            $result[] = $row_map;
        }

        return $result;
    }
}
