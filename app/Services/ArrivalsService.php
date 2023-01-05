<?php

namespace App\Services;

use App\Http\Controllers\FieldsMutationController;
use App\Models\Candidate;
use App\Models\Candidate_arrival;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArrivalsService
{
    public function updateTransportation($transportation_id, $place_arrive_id, $date_arrive)
    {
        $arrivals = Candidate_arrival::where('transportation_id', $transportation_id)->get();

        if ($arrivals) {
            foreach ($arrivals as $arrival) {
                $arrival->transportation_id = $transportation_id;
                $arrival->place_arrive_id = $place_arrive_id;
                $arrival->date_arrive = Carbon::parse($date_arrive);
                $arrival->save();

                $candidate = Candidate::find($arrival->candidate_id);

                if ($candidate) {
                    if ($candidate->active == 6 || $candidate->active == 19) {
                        $start = Carbon::now();

                        if ($arrival->date_arrive >= Carbon::now()->addDays(9)) {
                            $type = 14;
                            $start = Carbon::parse($arrival->date_arrive)->subDays(9);
                            $end = Carbon::parse($arrival->date_arrive)->subDays(9 - 3);
                        } elseif ($arrival->date_arrive >= Carbon::now()->addDays(5)) {
                            $type = 15;
                            $start = Carbon::parse($arrival->date_arrive)->subDays(5);
                            $end = Carbon::parse($arrival->date_arrive)->subDays(5 - 3);
                        } elseif ($arrival->date_arrive >= Carbon::now()->addDays(1)) {
                            $type = 16;
                            $start = Carbon::parse($arrival->date_arrive)->subDays(1);
                            $end = Carbon::parse($arrival->date_arrive);
                        } elseif ($arrival->date_arrive >= Carbon::now()->format('Y-m-d')) {
                            $type = 17;
                            $start = Carbon::parse($arrival->date_arrive);
                            $end = Carbon::parse($arrival->date_arrive)->addDays(1);
                        }

                        if ($start) {
                            Task::where('candidate_id', $candidate->id)
                                ->whereIn('status', [1, 3])
                                ->update(['status' => 2]);

                            $logists = User::where('group_id', 4)->where('activation', 1)->get();
                            foreach ($logists as $logist) {
                                $task = new Task();
                                $task->start = $start;
                                $task->end = $end;
                                $task->autor_id = Auth::user()->id;
                                $task->to_user_id = $logist->id;
                                $task->status = 1;
                                $task->type = $type;
                                $task->title = Task::getTypeTitle($task->type);
                                $task->candidate_id = $candidate->id;
                                $task->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
