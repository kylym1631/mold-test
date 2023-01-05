<?php

namespace App\Http\Controllers;

use App\Models\C_file;
use App\Models\Candidate;
use App\Models\Candidate_arrival;
use App\Models\History_candidate;
use App\Models\Task;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\FieldsMutation;
use App\Models\Blacklist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\FieldsMutationController;
use App\Http\Controllers\VacancyController;
use App\Models\CandidateDocument;
use App\Models\CandidateHousing;
use App\Models\CandidateLegalisation;
use App\Models\CandidatePosition;
use App\Services\CandidatesService;
use App\Services\WorkLogsService;
use Illuminate\Validation\Rules\File;

class CandidateController extends Controller
{
    public function getIndex()
    {
        $invited = null;
        $verif = null;
        $work = null;
        $cost_pay = null;

        if (
            !Auth::user()->isRecruiter()
            && !Auth::user()->isAdmin()
            && !Auth::user()->isLegalizationManager()
            && !Auth::user()->isRecruitmentDirector()
            && !Auth::user()->isKoordinator()
        ) {
            $invited = Candidate::where('removed', false)->where('user_id', Auth::user()->id)->count();
            $verif = Candidate::where('removed', false)->where('user_id', Auth::user()->id)->whereNotIn('active', [1, 2])->count();
            $work = Candidate::where('removed', false)->where('user_id', Auth::user()->id)->whereNotIn('active', [8, 9, 10])->count();
            $cost_pay = Candidate::where('removed', false)->where('user_id', Auth::user()->id)
                ->whereNotIn('active', [8, 9, 10])->sum('cost_pay');
        }

        $filters = array(
            'recruiters' => array(),
            'citizenship' => array(),
            'country' => array(),
            'statuses' => array(),
        );

        $candidates = Candidate::select('recruiter_id', 'removed', 'citizenship_id', 'country_id')
            ->whereNotNull('recruiter_id')
            ->where('removed', false)
            ->with('Recruiter')
            ->with('Citizenship')
            ->with('Country')
            ->get();

        foreach ($candidates as $c) {
            if ($c->Recruiter != null) {
                $filters['recruiters'][$c->Recruiter->id] = $c->Recruiter->firstName . ' ' . $c->Recruiter->lastName;
            }

            if ($c->Citizenship != null) {
                $filters['citizenship'][$c->Citizenship->id] = $c->Citizenship->name;
            }

            if ($c->Country != null) {
                $filters['country'][$c->Country->id] = $c->Country->name;
            }
        }

        $filters['statuses'] = array_map(function ($key) {
            return [
                'key' => $key,
                'name' => Candidate::getStatusTitle($key),
            ];
        }, Candidate::allowedStatusesToView());

        return view('candidates.index')
            ->with('cost_pay', $cost_pay)
            ->with('work', $work)
            ->with('verif', $verif)
            ->with('invited', $invited)
            ->with('filters', $filters);
    }

    public function getJson(Request $req, CandidatesService $cs)
    {
        $draw = request()->get('draw');
        $view = request()->get('view');
        $work_log_period = request('work_log_period');

        $list = $cs->getList($req, 'candidate');
        $data = $cs->getResultData($list['data'], 'candidate', $view);

        return response()->json([
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $list['filtered_count'],
            'recordsFiltered' => $list['filtered_count'],
        ], 200);
    }

    public function getStatusesCountJson()
    {
        $candidates = [];

        if (Auth::user()->isKoordinator()) {
            $candidates = Candidate::select('recruiter_id', 'removed', 'active', 'worked')
                ->where('removed', false)
                ->whereHas('Client', function ($q) {
                    $q->where('coordinator_id', Auth::user()->id);
                })
                ->get();
        } else if (Auth::user()->isRecruiter()) {
            $candidates = Candidate::select('recruiter_id', 'removed', 'active', 'worked')
                ->where('removed', false)
                ->where('recruiter_id', Auth::user()->id)
                ->get();
        } else {
            $candidates = Candidate::select('recruiter_id', 'removed', 'active', 'worked')
                ->where('removed', false)
                ->get();
        }

        $st_count = array();

        foreach ($candidates as $c) {
            if (!isset($st_count['s' . $c->active])) {
                $st_count['s' . $c->active] = 1;
            } else {
                $st_count['s' . $c->active]++;
            }

            if ($c->worked) {
                if (!isset($st_count['worked'])) {
                    $st_count['worked'] = 1;
                } else {
                    $st_count['worked']++;
                }
            }
        }

        // $sort_map = [
        //     's1' => null, 
        //     's14' => null, 
        //     's21' => null, 
        //     's4' => null, 
        //     's6' => null, 
        //     's19' => null, 
        //     's8' => null, 
        //     's7' => null, 
        //     's22' => null, 
        //     's9' => null, 
        //     's11' => null, 
        //     's5' => null,
        // ];

        // foreach ($st_count as $key => $value) {
        //     if ($value > 0 && array_key_exists($key, $sort_map)) {
        //         $name = Candidate::getStatusTitle(str_replace('s', '', $key));

        //         if ($key == 's14') {
        //             $name .= ' (Рекрутер)';
        //         }

        //         if ($key == 's21') {
        //             $name .= ' (Логист)';
        //         }

        //         $sort_map[$key] = array(
        //             'name' => $name,
        //             'count' => $value,
        //         );
        //     }
        // }

        $result = [
            'total' => [
                'count' => count($candidates),
            ],
        ];

        foreach ($st_count as $key => $val) {
            $result[$key] = [
                'count' => $val,
            ];
        }

        return response(array('success' => "true", 'data' => $result), 200);
    }

    public function positionsJson(Request $req)
    {
        $items = CandidatePosition::where('candidate_id', $req->candidate_id)
            ->orderBy('id', 'DESC')
            ->with('Position')
            ->with('Position.Client')
            ->get();

        $data = [];

        foreach ($items as $k => $m) {
            $position = '';
            $client = '';

            if ($m->Position) {
                $position = $m->Position->title;

                if ($m->Position->Client) {
                    $client = $m->Position->Client->name;
                }
            }

            $data[] = [
                'id' => $m->id,
                'client' => $client,
                'position' => $position,
                'start' => $m->start_at ? Carbon::parse($m->start_at)->format('d.m.Y') : '',
                'end' => $m->end_at ? Carbon::parse($m->end_at)->format('d.m.Y') : '',
                'is_current' => $k == 0 ? true : false,
                'candidate_id' => $m->candidate_id,
            ];
        }

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($items),
            'recordsFiltered' => count($items),
        ], 200);
    }

    public function housingJson(Request $req)
    {
        $items = CandidateHousing::where('candidate_id', $req->candidate_id)
            ->orderBy('id', 'DESC')
            ->with('Housing')
            ->with('Housing_room')
            ->with('Candidate')
            ->get();

        $data = [];

        foreach ($items as $k => $m) {
            $housing = '';
            $cost_per_day = '';
            $housing_room = '';

            if ($m->Housing) {
                $housing = $m->Housing->title . ' ' . $m->Housing->address;
                $cost_per_day = $m->Housing->cost_per_day;
            }

            if ($m->Housing_room) {
                $housing_room = $m->Housing_room->number;
            }

            $data[] = [
                'id' => $m->id,
                'housing' => $housing,
                'cost_per_day' => $cost_per_day,
                'housing_room' => $housing_room,
                'start' => $m->start_at ? Carbon::parse($m->start_at)->format('d.m.Y') : '',
                'end' => $m->end_at ? Carbon::parse($m->end_at)->format('d.m.Y') : '',
                'is_current' => $k == 0 && !$m->Candidate->own_housing ? true : false,
                'candidate_id' => $m->candidate_id,
            ];
        }

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($items),
            'recordsFiltered' => count($items),
        ], 200);
    }

    public function documentsJson(Request $req)
    {
        $items = CandidateDocument::where('candidate_id', $req->candidate_id)
            ->orderBy('id', 'DESC')
            ->get();

        $data = [];

        foreach ($items as $k => $m) {
            $housing = '';
            $cost_per_day = '';
            $housing_room = '';

            if ($m->Housing) {
                $housing = $m->Housing->title . ' ' . $m->Housing->address;
                $cost_per_day = $m->Housing->cost_per_day;
            }

            if ($m->Housing_room) {
                $housing_room = $m->Housing_room->number;
            }

            $data[] = [
                'id' => $m->id,
                'title' => $m->title,
                'date' => Carbon::parse($m->created_at)->format('d.m.Y'),
            ];
        }

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($items),
            'recordsFiltered' => count($items),
        ], 200);
    }

    public static function setStatus(Request $r)
    {
        $c_srv = new CandidatesService;

        $candidate = Candidate::find($r->id);

        if (
            Auth::user()->group_id > 99
            && (!Auth::user()->hasPermission('candidate.edit.status.' . $candidate->active)
                && !Auth::user()->hasPermission('employee.edit.status.' . $candidate->active))
        ) {
            return response(array('success' => "false", 'error' => 'У вас недостаточно прав для редактирования кандидата'), 200);
        }

        $candidate->count_failed_call = 0;

        if ($r->s == 4) {
            if ($candidate->Vacancy == null) {
                return response(array('success' => "false", 'error' => 'Ошибка: необходимо выбрать вакансию'), 200);
            }

            if ($candidate->D_file == null) {
                return response(array('success' => "false", 'error' => 'Загрузите документ'), 200);
            }
        }

        if ($r->s == 6 || $r->s == 19) {
            if (!Candidate_arrival::where('candidate_id', $candidate->id)->count()) {
                return response(array(
                    'success' => "false",
                    'error' => 'Добавте хоть один приезд',
                    'errCode' => 'ADD_ARRIVAL',
                    'candidateId' => $candidate->id,
                ), 200);
            }
        }

        // if ($r->s == 8 || $r->s == 12) {
        //     $arrival = Candidate_arrival::select('date_arrive')
        //         ->where('candidate_id', $candidate->id)
        //         ->where('active', true)
        //         ->latest()
        //         ->first();

        //     if (!$arrival || $arrival->date_arrive > Carbon::now()) {
        //         return response(array('success' => "false", 'error' => 'Кандидат еще не приехал'), 200);
        //     }
        // }

        if ($r->s == 7 || $r->s == 8) {
            if (!$candidate->Client) {
                return response(array('success' => "false", 'error' => 'Добавьте клиента'), 200);
            }
        }

        if ($r->s == 10) {
            $validator = Validator::make($r->all(), [
                'date_of_documents_importance' => 'required|date',
                'doc_type_id' => 'required|numeric',
                'file' => 'required',
                'file.*' => [
                    'required',
                    File::types(['jpeg', 'jpg', 'png', 'pdf'])->max(5 * 1024),
                ],
            ], [], [
                'date_of_documents_importance' => '«Дата важности документа»',
                'doc_type_id' => '«Тип документа»',
                'file' => '«Файл документа»',
            ]);
    
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        }

        if (Auth::user()->isRecruiter()) {
            if ($r->s == 2) {
                return response(array('success' => "false", 'error' => 'Статус «Лид» ставить нельзя'), 200);
            }
        }

        if ($candidate != null) {
            if ($r->s != '') {
                Task::where('candidate_id', $candidate->id)
                    ->whereIn('status', [1, 3])
                    ->update(['status' => 2]);

                $history = new History_candidate();
                $history->preview_value = $candidate->active || 0;
                $history->new_value = $r->s;
                $history->user_id = Auth::user()->id;
                $history->table_id = 'candidates_active';
                $history->save();
            }

            FieldsMutationController::addLog($r, $candidate, 'Candidate.setStatus');

            $candidate->active = $r->s;
            $candidate->active_update = Carbon::now();
            $candidate->reason_reject = $r->r;
            $candidate->save();

            if (Auth::user()->isFreelancer()) {
                if ($candidate->active == 2) {
                    $task = new Task();
                    $task->start = Carbon::now();
                    $task->title = 'Обработать лид';
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = Auth::user()->recruter_id;
                    $task->status = 1;
                    $task->type = 3;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            if ($candidate->active == 3) {
                $rejects_count = FieldsMutation::where('model_name', 'Candidate')
                    ->where('model_obj_id', $candidate->id)
                    ->where('field_name', 'active')
                    ->where('current_value', '3')
                    ->count();

                if (Auth::user()->isRecruiter()) {
                    if ($rejects_count >= 3) {
                        $rfm = $r;
                        $rfm->s = 5;
                        $rfm->r = 'Автоматический отказ';

                        FieldsMutationController::addLog($rfm, $candidate, 'Candidate.setStatus');

                        $candidate->active = 5;
                        $candidate->reason_reject = 'Автоматический отказ';
                        $candidate->save();
                    } else {
                        $task = new Task();
                        $task->start = Carbon::now();
                        $task->autor_id = Auth::user()->id;
                        $task->to_user_id = Auth::user()->id;
                        $task->status = 1;
                        $task->type = 2;
                        $task->title = Task::getTypeTitle($task->type);
                        $task->candidate_id = $candidate->id;
                        $task->save();
                    }
                } elseif (Auth::user()->isFreelancer()) {
                    if ($rejects_count >= 3) {
                        $rfm = $r;
                        $rfm->s = 5;
                        $rfm->r = 'Автоматический отказ';

                        FieldsMutationController::addLog($rfm, $candidate, 'Candidate.setStatus');

                        $candidate->active = 5;
                        $candidate->reason_reject = 'Автоматический отказ';
                        $candidate->save();
                    } else {
                        $task = new Task();
                        $task->start = Carbon::now();
                        $task->autor_id = Auth::user()->id;
                        $task->to_user_id = Auth::user()->id;
                        $task->status = 1;
                        $task->type = 2;
                        $task->title = Task::getTypeTitle($task->type);
                        $task->candidate_id = $candidate->id;
                        $task->save();
                    }
                } elseif (Auth::user()->isTrud()) {
                    $rfm = $r;
                    $rfm->s = 5;
                    $rfm->r = 'Отказ от трудоустройства';

                    FieldsMutationController::addLog($rfm, $candidate, 'Candidate.setStatus');

                    $candidate->active = 5;
                    $candidate->reason_reject = 'Отказ от трудоустройства';
                    $candidate->save();
                } else {
                    if ($rejects_count >= 3) {
                        $rfm = $r;
                        $rfm->s = 5;
                        $rfm->r = 'Автоматический отказ';

                        FieldsMutationController::addLog($rfm, $candidate, 'Candidate.setStatus');

                        $candidate->active = 5;
                        $candidate->reason_reject = 'Автоматический отказ';
                        $candidate->save();
                    } else {
                        $task = new Task();
                        $task->start = Carbon::now();
                        $task->end = Carbon::now()->addDays(1);
                        $task->autor_id = Auth::user()->id;
                        $task->to_user_id = $candidate->recruiter_id;
                        $task->status = 1;
                        $task->type = 18;
                        $task->title = Task::getTypeTitle($task->type);
                        $task->candidate_id = $candidate->id;
                        $task->save();
                    }
                }
            }

            if ($candidate->active == 4 || $candidate->active == 20) {
                if ($candidate->active == 4) {
                    $type = 4;
                } elseif ($candidate->active == 20) {
                    $type = 19;
                }

                $logists = User::where('group_id', 4)->where('activation', 1)->get();

                foreach ($logists as $logist) {
                    $task = new Task();
                    $task->start = Carbon::now();
                    $task->end = Carbon::now()->addDays(3);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $logist->id;
                    $task->status = 1;
                    $task->type = $type;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            if ($candidate->active == 12) {
                $task = new Task();
                $task->start = Carbon::now();
                $task->end = Carbon::now()->addDays(1);
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = Auth::user()->id;
                $task->status = 1;
                $task->type = 1;
                $task->title = Task::getTypeTitle($task->type);
                $task->candidate_id = $candidate->id;
                $task->save();
            }

            if ($candidate->active == 6) {
                $arrival = Candidate_arrival::where('candidate_id', $candidate->id)->latest()->first();
                $start = Carbon::now();
                $end = Carbon::now()->addDays(1);
                $type = 17;

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

            if ($candidate->active == 8) {
                $task = new Task();
                $task->start = Carbon::now();
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = $candidate->Client->coordinator_id;
                $task->status = 1;
                $task->type = 22;
                $task->title = Task::getTypeTitle($task->type);
                $task->candidate_id = $candidate->id;
                $task->save();
            }

            if ($candidate->active == 7) {
                $task = new Task();
                $task->start = Carbon::now();
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = $candidate->Client->coordinator_id;
                $task->status = 1;
                $task->type = 6;
                $task->title = Task::getTypeTitle($task->type);
                $task->candidate_id = $candidate->id;
                $task->save();

                if ($r->housing_id && $r->housing_room_id && $r->residence_started_at) {
                    $started_at = Carbon::createFromFormat('d.m.Y', $r->residence_started_at)->startOfDay();

                    if ($r->own_housing != 1) {
                        $c_srv->updateHousing($candidate, $r->housing_id, $r->housing_room_id, $started_at);
                    }

                    $candidate->housing_id = $r->housing_id;
                    $candidate->housing_room_id = $r->housing_room_id;
                    $candidate->residence_started_at = $started_at;
                }

                if ($r->own_housing == 1) {
                    $candidate->own_housing = 1;

                    $candidate->housing_id = null;
                    $candidate->housing_room_id = null;
                    $candidate->residence_started_at = null;
                }

                $candidate->save();
            }

            if ($candidate->active == 10) {
                $legal = new CandidateLegalisation;

                $legal->date_from = Carbon::now();
                $legal->date_to = Carbon::createFromFormat('d.m.Y', $r->date_of_documents_importance);
                $legal->doc_type_id = $r->doc_type_id;
                $legal->candidate_id = $candidate->id;
                $legal->user_id = Auth::user()->id;

                $legal->save();


                if ($fErr = self::checkFilesDocAdd($r)) {
                    return response($fErr, 200);
                }

                $cc = new CandidateController;

                if ($candidate->type_doc_id == $r->doc_type_id) {
                    $r->fileType = ['3'];

                    $cc->filesDocAdd($r, $candidate, $legal->id);
                } else {
                    $r->fileType = ['7'];

                    $cc->filesDocAdd($r, $candidate, $legal->id);
                }

                $users = User::where('group_id', 12)->where('activation', 1)->get();

                foreach ($users as $user) {
                    $task = new Task();
                    $task->start = Carbon::now();
                    $task->end = Carbon::now()->addHours(24);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $user->id;
                    $task->status = 1;
                    $task->type = 26;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            if ($candidate->active == 11) {
                if ($candidate->housing_id) {
                    $candidate->residence_stopped_at = Carbon::createFromFormat('d.m.Y', $r->residence_stopped_at);

                    $c_srv->updateHousing($candidate, null, null, null, Carbon::createFromFormat('d.m.Y', $r->residence_stopped_at)->startOfDay());
                }

                $candidate->housing_id = null;
                $candidate->housing_room_id = null;
                $candidate->own_housing = 1;

                $candidate->save();
            }

            if ($candidate->active == 19) {
                $arrival = Candidate_arrival::where('candidate_id', $candidate->id)->latest()->first();

                $trudos = User::where('group_id', 5)->where('activation', 1)->get();

                foreach ($trudos as $trudo) {
                    $task = new Task();
                    $task->start = Carbon::parse($arrival->date_arrive);
                    $task->end = Carbon::parse($arrival->date_arrive)->addDays(1);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $trudo->id;
                    $task->status = 1;
                    $task->type = 9;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            return response(array('success' => "true"), 200);
        } else {
            return response(array('success' => "false", 'error' => 'Ошибка'), 200);
        }
    }

    public static function setStatusSpecial(Request $r)
    {
        $c_srv = new CandidatesService;

        $candidate = Candidate::find($r->id);

        if (
            Auth::user()->group_id > 99
            && (!Auth::user()->hasPermission('candidate.edit.status.' . $candidate->active)
                && !Auth::user()->hasPermission('employee.edit.status.' . $candidate->active))
        ) {
            return response(array('success' => "false", 'error' => 'У вас недостаточно прав для редактирования кандидата'), 200);
        }

        if ($candidate != null) {
            FieldsMutationController::addLog($r, $candidate, 'Candidate.setStatus.special');
            $candidate->active_update = Carbon::now();

            if ($r->status != '') {
                Task::where('candidate_id', $candidate->id)
                    ->whereIn('status', [1, 3])
                    ->update(['status' => 2]);

                $history = new History_candidate();
                $history->preview_value = $candidate->active;
                $history->new_value = $r->status;
                $history->user_id = Auth::user()->id;
                $history->table_id = 'candidates_active';
                $history->save();
            }


            if ($r->status == 9) {
                $c_srv->updatePositions($candidate, $r->client_position_id, Carbon::createFromFormat('d.m.Y', $r->date)->startOfDay());

                $candidate->active = $r->status;
                $candidate->date_start_work = Carbon::createFromFormat('d.m.Y', $r->date);
                $candidate->client_position_id = $r->client_position_id;
                $candidate->reason_reject = $r->comment;


                if (Carbon::createFromFormat('d.m.Y', $r->date) <= Carbon::now()->subDays(7)) {
                    $candidate->worked = true;
                } else {
                    $candidate->worked = false;
                }

                $candidate->save();

                $users = User::where('group_id', 12)->where('activation', 1)->get();

                foreach ($users as $user) {
                    $task = new Task();
                    $task->start = Carbon::now();
                    $task->end = Carbon::now()->addHours(24);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $user->id;
                    $task->status = 1;
                    $task->type = 24;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();

                    $task = new Task();
                    $task->start = Carbon::now();
                    $task->end = Carbon::now()->addHours(24);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $user->id;
                    $task->status = 1;
                    $task->type = 25;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            if ($r->status == 14) {
                $candidate->active = $r->status;
                $candidate->reason_reject = $r->comment;
                $candidate->save();

                $task = new Task();
                $task->start = Carbon::createFromFormat('d.m.Y H:i', $r->date);
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = Auth::user()->id;
                $task->status = 1;
                $task->type = 10;
                $task->title = Task::getTypeTitle($task->type) . $r->comment;
                $task->candidate_id = $candidate->id;
                $task->save();
            }

            if ($r->status == 21) {
                $candidate->active = $r->status;
                $candidate->reason_reject = $r->comment;
                $candidate->save();

                $logists = User::where('group_id', 4)->where('activation', 1)->get();

                foreach ($logists as $logist) {
                    $task = new Task();
                    $task->start = Carbon::createFromFormat('d.m.Y H:i', $r->date);
                    $task->autor_id = Auth::user()->id;
                    $task->to_user_id = $logist->id;
                    $task->status = 1;
                    $task->type = 10;
                    $task->title = Task::getTypeTitle($task->type) . $r->comment;
                    $task->candidate_id = $candidate->id;
                    $task->save();
                }
            }

            if ($r->status == 22) {
                $candidate->active = $r->status;
                $candidate->reason_reject = $r->comment;
                $candidate->save();

                $bl = new Blacklist;
                $bl->user_id = Auth::user()->id;
                $bl->firstName = $candidate->firstName;
                $bl->lastName = $candidate->lastName;
                $bl->phone = $candidate->phone;
                $bl->inn = $candidate->inn;
                $bl->candidate_id = $candidate->id;
                $bl->comment = $r->comment;
                $bl->save();
            }

            return response(array('success' => "true"), 200);
        } else {
            return response(array('success' => "false", 'error' => 'Кандидат не найден'), 200);
        }
    }

    public function addIndex(Request $r)
    {
        if (Auth::user()->isFreelancer()) {
            if (Auth::user()->fl_status != 2) {
                return response(array('success' => "false"), 200);
            }
        }

        $vacancy = null;

        if ($r->has('id')) {
            $canddaite = Candidate::where('id', $r->id)->where('removed', false)
                ->with('Vacancy')
                ->with('Citizenship')
                // ->with('Nacionality')
                ->with('Country')
                ->with('Type_doc')
                ->with('Logist_place_arrive')
                ->with('Real_status_work')
                ->with('Transport')
                ->with('Client')
                ->with('Client_position')
                ->first();

            if (!$canddaite) {
                return abort(404);
            }

            $reason_reject = $canddaite->reason_reject;
            if ($reason_reject != '') {
                $reason_reject = '<br> ' . $canddaite->reason_reject;
            }
            $count_failed_call_btn = '';
            if ($canddaite->active == 15) {
                $count_failed_call_btn = '<button  onclick="setCountFailCall(' . $canddaite->id . ',' . $canddaite->count_failed_call . ')">Недозвон ' . ($canddaite->count_failed_call + 1) . '</button>';
            }
            $select_active = '<select class="js-select-status form-select form-select-sm form-select-solid" data-action="setCandidateStatus" data-candidate-id="' . $canddaite->id . '">' . $canddaite->getStatusOptions() . '</select>' . $reason_reject . $count_failed_call_btn;
        } else {
            $canddaite = null;
            $select_active = null;

            if ($r->has('vid')) {
                $vacancy = Vacancy::find($r->vid);
            }
        }

        $recruter = null;
        if ($r->has('r_id')) {
            $recruter = User::find($r->r_id);
        } else {
            if ($canddaite != null) {
                $recruter = User::find($canddaite->recruiter_id);
            }
        }


        return view('candidates.add')
            ->with('select_active', $select_active)
            ->with('recruter', $recruter)
            ->with('vacancy', $vacancy)
            ->with('candidate', $canddaite)
            ->with('is_add_page', !$r->has('id'));
    }

    public function viewIndex(Request $r)
    {

        if (Auth::user()->isFreelancer()) {
            if (Auth::user()->fl_status != 2) {
                return response(array('success' => "false"), 200);
            }
        }

        $vacancy = null;


        if ($r->has('id')) {
            $canddaite = Candidate::where('id', $r->id)->where('removed', false)
                ->with('Vacancy')
                ->with('Citizenship')
                // ->with('Nacionality')
                ->with('Country')
                ->with('Type_doc')
                ->with('Logist_place_arrive')
                ->with('Real_status_work')
                ->with('Transport')
                ->with('Client')
                ->with('Housing')
                ->with('Housing_room')
                ->with('Legalisation')
                ->first();

            if (!$canddaite) {
                return abort(404);
            }

            $reason_reject = $canddaite->reason_reject;
            if ($reason_reject != '') {
                $reason_reject = '<br> ' . $canddaite->reason_reject;
            }
            $count_failed_call_btn = '';
            if ($canddaite->active == 15) {
                $count_failed_call_btn = '<button  onclick="setCountFailCall(' . $canddaite->id . ',' . $canddaite->count_failed_call . ')">Недозвон ' . ($canddaite->count_failed_call + 1) . '</button>';
            }
            $select_active = '<select onchange="changeActivation(' . $canddaite->id . ',' . $canddaite->count_failed_call . ')"
                                    class="form-select form-select-sm form-select-solid changeActivation' . $canddaite->id . '">
                                        <option value="">Статус</option>
                                             ' . $canddaite->getStatusOptions() . '
                            </select>' . $reason_reject . $count_failed_call_btn;

            if ($canddaite->date_start_work) {
                $canddaite->date_start_work = Carbon::parse($canddaite->date_start_work)->format('d.m.Y');
            }
        } else {
            return response(array('success' => "false", 'error' => 'candidate not found'), 200);
        }

        $recruter = null;
        if ($r->has('r_id')) {
            $recruter = User::find($r->r_id);
        } else {
            if ($canddaite != null) {
                $recruter = User::find($canddaite->recruiter_id);
            }
        }

        
        if ($canddaite->Legalisation) {
            $legal_date = $canddaite->Legalisation->date_to;
            if ($legal_date) {
                if ($legal_date > Carbon::now()) {
                    $canddaite->legal_days_left = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($legal_date)->startOfDay());
                } else {
                    $canddaite->legal_days_left = 0;
                }
            }
        }


        return view('candidates.view')
            ->with('select_active', $select_active)
            ->with('recruter', $recruter)
            ->with('vacancy', $vacancy)
            ->with('candidate', $canddaite)
            ->with('is_add_page', false);
    }

    /*
    Add|Update Candidate
    */
    public function postAdd(Request $r)
    {
        $c_srv = new CandidatesService;

        if (!$r->id && !isset($r->createFromLead)) {
            if (
                Auth::user()->isLegalizationManager()
                || (Auth::user()->group_id > 99 && !Auth::user()->hasPermission('candidate.create'))
            ) {
                return response(array('success' => "false", 'error' => 'У вас недостаточно прав для создания кандидата'), 200);
            }
        }

        if (Auth::user()->isFreelancer()) {
            if (Auth::user()->fl_status != 2) {
                return response(array('success' => "false"), 200);
            }
        }

        if (!isset($r->createFromLead)) {
            $niceNames = [
                'lastName' => '«Фамилия»',
                'firstName' => '«Имя»',
                'phone' => '«Телефон»',
                'viber' => '«Viber»',
                'dateOfBirth' => '«Дата рождения»',
                'gender' => '«Пол»',
            ];
            $validate_arr = [
                'lastName' => 'required',
                'firstName' => 'required',
                'phone' => 'required|regex:/^\+[0-9]{7,18}$/',
                'viber' => 'required|regex:/^\+[0-9]{7,18}$/',
                'dateOfBirth' => 'required|date_format:d.m.Y',
                'gender' => 'required',
            ];

            $validator = Validator::make($r->all(), $validate_arr, [], $niceNames);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }


            $diff_year = Carbon::now()->diffInYears(Carbon::createFromFormat('d.m.Y', $r->dateOfBirth));
            if ($diff_year < 18) {
                return response(array('success' => "false", 'error' => 'Возраст менее 18'), 200);
            }
        } else {
            $validator = Validator::make($r->all(), [
                'phone' => 'required|regex:/^\+[0-9]{7,18}$/|unique:candidates,phone',
            ], [], [
                'phone' => '«Телефон»',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        }

        $candidate = Candidate::find($r->id);

        if ($candidate == null) {
            if (!isset($r->createFromLead)) {
                if (Auth::user()->group_id > 99 && !Auth::user()->hasPermission('candidate.create')) {
                    return response(array('success' => "false", 'error' => 'У вас недостаточно прав для создания кандидата'), 200);
                }

                $validator = Validator::make($r->all(), [
                    'phone' => 'required|regex:/^\+[0-9]{7,18}$/|unique:candidates,phone',
                ], [], [
                    'phone' => '«Телефон»',
                ]);

                if ($validator->fails()) {
                    $error = $validator->errors()->first();
                    return response(array('success' => "false", 'error' => $error), 200);
                }
            }


            $is_new = true;
            $candidate = new Candidate();
            $candidate->user_id = Auth::user()->id;
            $candidate->active = 1;


            if (Auth::user()->isRecruiter() && empty($r->file) && !isset($r->createFromLead)) {
                return response(array('success' => "false", 'error' => 'Загрузите документ'), 200);
            }
        } else {

            if (
                Auth::user()->group_id > 99
                && (!Auth::user()->hasPermission('candidate.edit.status.' . $candidate->active)
                    && !Auth::user()->hasPermission('employee.edit.status.' . $candidate->active))
            ) {
                return response(array('success' => "false", 'error' => 'У вас недостаточно прав для редактирования кандидата'), 200);
            }

            $is_new = false;

            $validator = Validator::make($r->all(), [
                'phone' => 'required|regex:/^\+[0-9]{7,18}$/|unique:candidates,phone,' . $candidate->id,
            ], [], [
                'phone' => '«Телефон»',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }

            if (Auth::user()->isRecruiter() && $candidate->D_file == null && empty($r->file)) {
                return response(array('success' => "false", 'error' => 'Загрузите документ'), 200);
            }
        }

        if ($fErr = $this->checkFilesDocAdd($r)) {
            return response($fErr, 200);
        }

        if ($is_new == false) {
            FieldsMutationController::addLog($r, $candidate, 'Candidate');
        } else {
            if (
                $r->place_arrive_id
                || $r->transport_id
                || $r->date_arrive
            ) {
                $validatorArrivals = Validator::make($r->all(), [
                    'place_arrive_id' => 'required',
                    'transport_id' => 'required',
                    'date_arrive' => 'required'
                ], [], [
                    'place_arrive_id' => '«Место приезда»',
                    'transport_id' => '«Вид транспорта»',
                    'date_arrive' => '«Дата и время приезда»',
                ]);

                if ($validatorArrivals->fails()) {
                    $error = $validatorArrivals->errors()->first();
                    return response(array('success' => "false", 'error' => $error), 200);
                }

                if (Carbon::parse($r->date_arrive) <= Carbon::now()->addHours(5)) {
                    return response(array('success' => "false", 'error' => 'Укажите актуальную дату приезда'), 200);
                }
            }
        }


        // if (Auth::user()->isFreelancer()) {
        //     $candidate->recruiter_id = Auth::user()->recruter_id;
        // }
        if (Auth::user()->isRecruiter()) {
            $candidate->recruiter_id = Auth::user()->id;
        }
        if (Auth::user()->isAdmin()) {
            $candidate->recruiter_id = $r->recruiter_id;
        }

        if (!$candidate->recruiter_id && !Auth::user()->isFreelancer()) {
            return response(array('success' => "false", 'error' => 'Укажите рекрутера'), 200);
        }


        $candidate->lastName = $r->lastName;
        $candidate->firstName = $r->firstName;
        $candidate->phone = $r->phone;
        $candidate->viber = $r->viber;

        if ($r->dateOfBirth != '' && $r->dateOfBirth != 'undefined') {
            $candidate->dateOfBirth = Carbon::createFromFormat('d.m.Y', $r->dateOfBirth);
        }
        if ($r->phone_parent != '' && $r->phone_parent != 'undefined') {
            $candidate->phone_parent = $r->phone_parent;
        }
        if ($r->citizenship_id != '' && $r->citizenship_id != 'undefined') {
            $candidate->citizenship_id = $r->citizenship_id;
        }
        // if ($r->nacionality_id != '' && $r->nacionality_id != 'undefined') {
        //     $candidate->nacionality_id = $r->nacionality_id;
        // }
        if ($r->speciality_id != '' && $r->speciality_id != 'undefined') {
            $candidate->speciality_id = $r->speciality_id;
        }
        if ($r->country_id != '' && $r->country_id != 'undefined') {
            $candidate->country_id = $r->country_id;
        }
        // if ($r->date_arrive != '' && $r->date_arrive != 'undefined') {
        //     if (Carbon::now()->diffInYears(Carbon::createFromFormat('d.m.Y', $r->date_arrive)) > 10) {
        //         return response(array('success' => "false", 'error' => 'Исправте дату приезда'), 200);
        //     }
        //     $candidate->date_arrive = Carbon::createFromFormat('d.m.Y', $r->date_arrive);
        // }
        if ($r->type_doc_id != '' && $r->type_doc_id != 'undefined') {
            $candidate->type_doc_id = $r->type_doc_id;
        }
        if ($r->transport_id != '' && $r->transport_id != 'undefined') {
            $candidate->transport_id = $r->transport_id;
        }
        if ($r->inn != '' && $r->inn != 'undefined') {
            $candidate->inn = $r->inn;
        }
        if ($r->comment != '' && $r->comment != 'undefined') {
            $candidate->comment = $r->comment;
        }

        // logist
        if ($r->logist_date_arrive != '' && $r->logist_date_arrive != 'undefined') {

            $candidate->logist_date_arrive = Carbon::createFromFormat('d.m.Y H:i', $r->logist_date_arrive);
        }
        if ($r->logist_place_arrive_id != '' && $r->logist_place_arrive_id != 'undefined') {
            $candidate->logist_place_arrive_id = $r->logist_place_arrive_id;
        }
        // logist

        // trudo
        if ($r->real_vacancy_id != '' && $r->real_vacancy_id != 'undefined') {
            $candidate->real_vacancy_id = $r->real_vacancy_id;
        }
        if ($r->client_id != '' && $r->client_id != 'undefined') {
            $candidate->client_id = $r->client_id;
        }

        if ($r->client_position_id != '' && $r->client_position_id != 'undefined') {

            $c_srv->updatePositions($candidate, $r->client_position_id);

            $candidate->client_position_id = $r->client_position_id;
        }

        if ($r->real_status_work_id != '' && $r->real_status_work_id != 'undefined') {
            $candidate->real_status_work_id = $r->real_status_work_id;
        }
        if ($r->gender) {
            $candidate->gender = $r->gender;
        }
        $candidate->pesel = $r->pesel ?: null;
        $candidate->account_number = $r->account_number ?: null;
        $candidate->mothers_name = $r->mothers_name ?: null;
        $candidate->fathers_name = $r->fathers_name ?: null;
        $candidate->address = $r->address ?: null;
        $candidate->city = $r->city ?: null;
        $candidate->zip = $r->zip ?: null;

        if (isset($r->is_housing_block)) {
            if ($r->own_housing) {
                if ($candidate->housing_id) {
                    $candidate->residence_stopped_at = Carbon::now();

                    $c_srv->updateHousing($candidate, null, null, null, Carbon::now()->startOfDay());
                }
                $candidate->housing_id = null;
                $candidate->housing_room_id = null;
                $candidate->own_housing = 1;
            } else {
                $started_at = $r->residence_started_at
                    ? Carbon::createFromFormat('d.m.Y', $r->residence_started_at)->startOfDay()
                    : null;

                $c_srv->updateHousing($candidate, $r->housing_id, $r->housing_room_id, $started_at);

                $candidate->own_housing = 0;

                $candidate->housing_id = $r->housing_id ?: null;
                $candidate->housing_room_id = $r->housing_room_id ?: null;
                $candidate->residence_started_at = $started_at;
            }
        }

        $candidate->save();

        if ($r->file) {
            $this->filesDocAdd($r, $candidate, $is_new);
        }

        $candidate = Candidate::find($candidate->id);

        if ($candidate->Vacancy != null) {
            $candidate->cost_pay = $candidate->Vacancy->recruting_cost;
            $candidate->cost_pay_lead = $candidate->Vacancy->cost_pay_lead;
            $candidate->save();

            VacancyController::pauseIfFilled();
        }

        if ($is_new) {
            if (Auth::user()->isFreelancer()) {
                Task::where('to_user_id', Auth::user()->id)
                    ->where('candidate_id', $candidate->id)
                    ->whereIn('status', [1, 3])
                    ->update(['status' => 2]);

                $task = new Task();
                $task->start = Carbon::now();
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = Auth::user()->id;
                $task->status = 1;
                $task->type = 1;
                $task->title = Task::getTypeTitle($task->type);
                $task->candidate_id = $candidate->id;
                $task->save();
            }

            if (Auth::user()->isRecruiter()) {
                Task::where('to_user_id', Auth::user()->id)
                    ->where('candidate_id', $candidate->id)
                    ->whereIn('status', [1, 3])
                    ->update(['status' => 2]);

                $task = new Task();
                $task->start = Carbon::now();
                $task->autor_id = Auth::user()->id;
                $task->to_user_id = Auth::user()->id;
                $task->status = 1;
                $task->type = 1;
                $task->title = Task::getTypeTitle($task->type);
                $task->candidate_id = $candidate->id;
                $task->save();
            }

            FieldsMutationController::addLog($r, $candidate, 'Candidate.New');
        }

        if (isset($r->createFromLead)) {
            return array('candidate_id' => $candidate->id);
        }

        if ($is_new && ($r->place_arrive_id || $r->transport_id || $r->date_arrive)) {
            $cArReq = $r;
            $cArReq->comment = $r->comment_arrive;
            $cArReq->candidate_id = $candidate->id;
            $cArReq->isAddNewCandidate = true;

            return CandidateArrivalsController::postArrivalAdd($cArReq);
        }

        return response()->json(['success' => 'true', 'id' => $candidate->id], 200);
    }

    private static function checkFilesDocAdd($r)
    {
        if (empty($r->file)) {
            return null;
        }

        foreach ($r->file as $fileItem) {
            if ($fileItem->isValid()) {
                $ext = $fileItem->getClientOriginalExtension();

                if ($ext != 'jpeg' && $ext != 'jpg' && $ext != 'png' && $ext != 'gif' && $ext != 'pdf') {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый тип файла'
                    );
                }

                if ($fileItem->getSize() > 5000000) {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый вес файла'
                    );
                }
            } else {
                return array(
                    'success' => "false",
                    'error' => 'Файл повреждён и не может быть загружен!'
                );
            }
        }
    }

    private function filesDocAdd($r, $candidate, $legal_id = null)
    {
        $c_id = $candidate->id;

        foreach ($r->file as $key => $fileItem) {
            if ($fileItem->isValid()) {
                $type = $r->fileType[$key];

                $path = '/uploads/candidate/' . Carbon::now()->format('m.Y') . '/' . $c_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $old_c_file = C_file::where('candidate_id', $c_id)->where('type', $type)->first();

                $old_c_name = null;

                if ($old_c_file) {
                    $old_c_name = $old_c_file->original_name;
                }

                FieldsMutationController::addFileLog($candidate, $type, $fileItem->getClientOriginalName(), $old_c_name);

                C_file::where('candidate_id', $c_id)->where('type', $type)->delete();

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file->candidate_id = $c_id;
                $file->user_id = Auth::user()->id;
                $file->type = $type;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->candidate_legalisation_id = $legal_id;
                $file->save();
            } else {
                return Response::json(array(
                    'success' => "false",
                    'error' => 'Файл повреждён и не может быть загружен!'
                ), 200);
            }
        }
    }

    public function updateHousing(Request $r, CandidatesService $c_srv)
    {
        if (!$r->own_housing) {
            $validator = Validator::make($r->all(), [
                'housing_id' => 'required|numeric',
                'housing_room_id' => 'required|numeric',
                'residence_started_at' => 'required',
            ], [], [
                'housing_id' => '«Жилье»',
                'housing_room_id' => '«Номер комнаты»',
                'residence_started_at' => '«Дата начала проживания»',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        }

        $candidate = Candidate::find($r->id);

        if ($r->own_housing) {
            if ($candidate->housing_id) {
                $r->residence_stopped_at = Carbon::now(); // for fields mutation
            }
        }

        FieldsMutationController::addLog($r, $candidate, 'Candidate');

        if ($r->own_housing) {
            if ($candidate->housing_id) {
                $candidate->residence_stopped_at = Carbon::now();

                $c_srv->updateHousing($candidate, null, null, null, Carbon::now()->startOfDay());
            }


            $candidate->housing_id = null;
            $candidate->housing_room_id = null;

            $candidate->own_housing = 1;
        } else {
            $started_at = Carbon::createFromFormat('d.m.Y', $r->residence_started_at)->startOfDay();

            $c_srv->updateHousing($candidate, $r->housing_id, $r->housing_room_id, $started_at);

            $candidate->own_housing = 0;

            $candidate->housing_id = $r->housing_id;
            $candidate->housing_room_id = $r->housing_room_id;
            $candidate->residence_started_at = $started_at;
        }

        $candidate->save();

        return response()->json(['success' => 'true', 'id' => $candidate->id], 200);
    }

    public function updateEmployment(Request $r, CandidatesService $c_srv)
    {
        $candidate = Candidate::find($r->id);

        FieldsMutationController::addLog($r, $candidate, 'Candidate');

        $prev_pos_count = CandidatePosition::where('candidate_id', $candidate->id)->count();

        if ($prev_pos_count < 1) {
            $candidate->date_start_work = Carbon::now()->startOfDay();
        }

        $c_srv->updatePositions($candidate, $r->client_position_id);

        $candidate->client_position_id = $r->client_position_id;
        $candidate->save();

        return response()->json(['success' => 'true', 'id' => $candidate->id], 200);
    }

    public function filesTicketAdd()
    {

        $c_id = request()->get('id');
        if ($c_id == '') {
            $candidate = new Candidate();
            $candidate->user_id = Auth::user()->id;
            $candidate->active = 1;
            $candidate->save();
            $c_id = $candidate->id;
        }

        $file = request()->file('file');
        if ($file->isValid()) {

            $path = '/uploads/candidate/' . Carbon::now()->format('m.Y') . '/' . $c_id . '/files/';
            $name = Str::random(12) . '.' . $file->getClientOriginalExtension();

            $file->move(public_path($path), $name);
            $file_link = $path . $name;


            $file = new C_file();
            $file->autor_id = Auth::user()->id;
            $file->candidate_id = $c_id;
            $file->user_id = Auth::user()->id;
            $file->type = 4;
            $file->original_name = request()->file('file')->getClientOriginalName();
            $file->ext = request()->file('file')->getClientOriginalExtension();
            $file->path = $file_link;
            $file->save();

            return Response::json(array(
                'success' => "true",
                'id' => $c_id,
                'path' => url('/') . '' . $file_link
            ), 200);
        } else {
            return Response::json(array(
                'success' => "false",
                'error' => 'file not valid!'
            ), 200);
        }
    }

    public function remove(Request $req)
    {
        if (empty($req->candidate_id)) {
            return response(array('success' => "false", 'error' => 'Error! candidate_id is empty'), 200);
        }

        $user = Candidate::find($req->candidate_id);
        $user->removed = true;
        $user->save();

        return response(array('success' => "true"), 200);
    }

    public function addVacancy(Request $req)
    {
        if (empty($req->candidateId) || empty($req->vacancyId)) {
            return response(array('success' => "false", 'error' => 'Error!'), 200);
        }

        $cand = Candidate::find($req->candidateId);
        $cand->real_vacancy_id = $req->vacancyId;
        $cand->save();

        return response(array('success' => "true"), 200);
    }

    public function addClient(Request $req)
    {
        if (empty($req->candidateId) || empty($req->clientId)) {
            return response(array('success' => "false", 'error' => 'Error!'), 200);
        }

        $cand = Candidate::find($req->candidateId);
        $cand->client_id = $req->clientId;
        $cand->save();

        return response(array('success' => "true"), 200);
    }

    public function setGender(Request $req)
    {
        if (empty($req->candidateId)) {
            return response(array('success' => "false", 'error' => 'Error!'), 200);
        }

        if (empty($req->gender)) {
            return response(array('success' => "false", 'error' => 'Укажите пол'), 200);
        }

        $cand = Candidate::find($req->candidateId);
        $cand->gender = $req->gender;
        $cand->save();

        return response(array('success' => "true"), 200);
    }

    public function updatePosition(Request $req)
    {
        $validator = null;

        if ($req->is_current == 'true') {
            $validator = Validator::make($req->all(), [
                'start_at' => 'required',
            ], [], [
                'start_at' => '«Начало»',
            ]);
        } else {
            $validator = Validator::make($req->all(), [
                'start_at' => 'required',
                'end_at' => 'required',
            ], [], [
                'start_at' => '«Начало»',
                'end_at' => '«Окончание»',
            ]);
        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = CandidatePosition::find($req->id);

        if (!$item) {
            return response(array('success' => "false", 'error' => 'Должность не найдена'), 200);
        }

        $start_at = $req->start_at ? Carbon::createFromFormat('d.m.Y', $req->start_at)->startOfDay() : null;
        $end_at = $req->end_at ? Carbon::createFromFormat('d.m.Y', $req->end_at)->startOfDay() : null;

        if ($start_at && $end_at) {
            if ($end_at < $start_at) {
                return response(array('success' => "false", 'error' => 'Дата окончания должна быть больше даты начала'), 200);
            }
        }

        $all_items = CandidatePosition::where('candidate_id', $req->candidate_id)
            ->orderBy('id', 'ASC')
            ->get();

        $prev_item = null;
        $next_item = null;

        foreach ($all_items as $key => $it) {
            if ($it->id == $item->id) {
                if (isset($all_items[$key - 1])) {
                    $prev_item = $all_items[$key - 1];
                }

                if (isset($all_items[$key + 1])) {
                    $next_item = $all_items[$key + 1];
                }
            }
        }

        if ($req->is_current == 'true') {
            if ($prev_item && Carbon::parse($prev_item->end_at)->startOfDay() >= $start_at) {
                return response(array('success' => "false", 'error' => 'Дата начала текущего периода должна быть больше даты окончания прошлого периода'), 200);
            }
        } else {
            if ($prev_item && Carbon::parse($prev_item->end_at)->startOfDay() >= $start_at) {
                return response(array('success' => "false", 'error' => 'Дата начала текущего периода должна быть больше даты окончания прошлого периода'), 200);
            }

            if ($next_item && Carbon::parse($next_item->start_at)->startOfDay() <= $end_at) {
                return response(array('success' => "false", 'error' => 'Дата окончания текущего периода должна быть меньше даты начала следующего периода'), 200);
            }
        }

        $item->start_at = $start_at;
        $item->end_at = $end_at;

        $item->save();

        return response(array('success' => "true"), 200);
    }

    public function updateHousingPeriod(Request $req)
    {
        $validator = null;

        if ($req->is_current == 'true') {
            $validator = Validator::make($req->all(), [
                'start_at' => 'required',
            ], [], [
                'start_at' => '«Начало»',
            ]);
        } else {
            $validator = Validator::make($req->all(), [
                'start_at' => 'required',
                'end_at' => 'required',
            ], [], [
                'start_at' => '«Начало»',
                'end_at' => '«Окончание»',
            ]);
        }

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = CandidateHousing::find($req->id);

        if (!$item) {
            return response(array('success' => "false", 'error' => 'Жилье не найдено'), 200);
        }

        $start_at = $req->start_at ? Carbon::createFromFormat('d.m.Y', $req->start_at)->startOfDay() : null;
        $end_at = $req->end_at ? Carbon::createFromFormat('d.m.Y', $req->end_at)->startOfDay() : null;

        if ($start_at && $end_at) {
            if ($end_at < $start_at) {
                return response(array('success' => "false", 'error' => 'Дата окончания должна быть больше даты начала'), 200);
            }
        }

        $all_items = CandidateHousing::where('candidate_id', $req->candidate_id)
            ->orderBy('id', 'ASC')
            ->get();

        $prev_item = null;
        $next_item = null;

        foreach ($all_items as $key => $it) {
            if ($it->id == $item->id) {
                if (isset($all_items[$key - 1])) {
                    $prev_item = $all_items[$key - 1];
                }

                if (isset($all_items[$key + 1])) {
                    $next_item = $all_items[$key + 1];
                }
            }
        }

        if ($req->is_current == 'true') {
            if ($prev_item && Carbon::parse($prev_item->end_at)->startOfDay() >= $start_at) {
                return response(array('success' => "false", 'error' => 'Дата начала текущего периода должна быть больше даты окончания прошлого периода'), 200);
            }
        } else {
            if ($prev_item && Carbon::parse($prev_item->end_at)->startOfDay() >= $start_at) {
                return response(array('success' => "false", 'error' => 'Дата начала текущего периода должна быть больше даты окончания прошлого периода'), 200);
            }

            if ($next_item && Carbon::parse($next_item->start_at)->startOfDay() <= $end_at) {
                return response(array('success' => "false", 'error' => 'Дата окончания текущего периода должна быть меньше даты начала следующего периода'), 200);
            }
        }

        $item->start_at = $start_at;
        $item->end_at = $end_at;

        $item->save();

        return response(array('success' => "true"), 200);
    }

    public function workLogsHistoryJson(Request $req, WorkLogsService $WLS)
    {
        $result = $WLS->getResultDataByPositions([$req->candidate_id], $req->period);

        return response()->json($result[0], 200);
    }
}
