<?php

namespace App\Services;

use App\Models\Vacancy;

class VacanciesService
{
    public function filling($v_id)
    {
        $vacancy = Vacancy::where('id', $v_id)
            ->select('id', 'count_men', 'count_women', 'count_people')
            ->with('Candidates')
            ->first();

        if (!$vacancy) {
            return false;
        }

        $vacancy->filled_men = 0;
        $vacancy->filled_women = 0;
        $vacancy->filled_it = 0;

        $is_filled = false;

        if ($vacancy->Candidates) {
            foreach ($vacancy->Candidates as $Candidate) {
                if ($Candidate->gender == 'm') {
                    $vacancy->filled_men++;
                } elseif ($Candidate->gender == 'f') {
                    $vacancy->filled_women++;
                } else {
                    $vacancy->filled_it++;
                }
            }
        }

        if ($vacancy->filled_men > $vacancy->count_men) {
            $vacancy->filled_it += ($vacancy->filled_men - $vacancy->count_men);

            $vacancy->filled_men = $vacancy->count_men;
        }

        if ($vacancy->filled_women > $vacancy->count_women) {
            $vacancy->filled_it += ($vacancy->filled_women - $vacancy->count_women);

            $vacancy->filled_women = $vacancy->count_women;
        }

        return [
            'men' => $vacancy->filled_men,
            'women' => $vacancy->filled_women,
            'it' => $vacancy->filled_it,
        ];
    }
}