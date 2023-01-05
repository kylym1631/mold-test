<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\FieldsMutation;
use App\Models\Candidate;
use App\Models\Candidate_arrival;
use App\Models\Handbook;
use App\Models\Client;
use App\Models\Client_position;
use App\Models\Vacancy;
use App\Models\Housing;
use App\Models\Housing_room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FieldsMutationController extends Controller
{
    public function getIndex()
    {
        return view('fields-mutation.index');
    }

    public function getJson(Request $request)
    {
        $draw = $request->draw;
        $start = $request->start;
        $rowperpage = $request->length;

        $users = null;
        $roles_ids = null;

        if ($draw == 1) {
            $roles_ids = array();

            foreach (FieldsMutation::getRolesArr() as $key => $value) {
                $roles_ids[] = array($key, $value);
            }
        }

        $id = $request->id;

        $filtered_count = $this->prepareGetJsonRequest($request, $id);
        $filtered_count = $filtered_count->count();

        $data = $this->prepareGetJsonRequest($request, $id);
        $data = $data->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $result = array();

        $user_ids = array();

        $handbook_ids = array(
            'citizenship_id' => array(),
            'nacionality_id' => array(),
            'country_id' => array(),
            'type_doc_id' => array(),
            'transport_id' => array(),
            'place_arrive_id' => array(),
            'real_status_work_id' => array(),
        );

        $relative_ids = array(
            'real_vacancy_id' => array(),
            'client_id' => array(),
            'recruiter_id' => array(),
            'housing_id' => array(),
            'housing_room_id' => array(),
            'client_position_id' => array(),
        );

        foreach ($data as $item) {
            if (isset($handbook_ids[$item['field_name']])) {
                if ($item['prev_value']) {
                    $handbook_ids[$item['field_name']][] = $item['prev_value'];
                }

                if ($item['current_value']) {
                    $handbook_ids[$item['field_name']][] = $item['current_value'];
                }
            }

            if (isset($relative_ids[$item['field_name']])) {
                if ($item['prev_value']) {
                    $relative_ids[$item['field_name']][] = $item['prev_value'];
                }

                if ($item['current_value']) {
                    $relative_ids[$item['field_name']][] = $item['current_value'];
                }
            }
        }

        $handbook_flat_ids = array();

        foreach ($handbook_ids as $arr) {
            foreach ($arr as $val) {
                $handbook_flat_ids[] = $val;
            }
        }

        $handbook = Handbook::whereIn('id', $handbook_flat_ids)->get();
        $handbook_values = array();

        $client = Client::whereIn('id', $relative_ids['client_id'])->get();
        $client_values = array();

        $client_position = Client_position::whereIn('id', $relative_ids['client_position_id'])->get();
        $client_position_values = array();

        $vacancy = Vacancy::whereIn('id', $relative_ids['real_vacancy_id'])->get();
        $vacancy_values = array();

        $recruiter = User::whereIn('id', $relative_ids['recruiter_id'])->get();
        $recruiter_values = array();

        $housing = Housing::whereIn('id', $relative_ids['housing_id'])->get();
        $housing_values = array();

        $housing_room = Housing_room::whereIn('id', $relative_ids['housing_room_id'])->get();
        $housing_room_values = array();

        foreach ($handbook as $item) {
            $handbook_values[$item->id] = $item->name;
        }

        foreach ($client as $item) {
            $client_values[$item->id] = $item->name;
        }

        foreach ($client_position as $item) {
            $client_position_values[$item->id] = $item->title;
        }

        foreach ($vacancy as $item) {
            $vacancy_values[$item->id] = $item->title;
        }

        foreach ($recruiter as $item) {
            $recruiter_values[$item->id] = mb_strtoupper($item->firstName . ' ' . $item->lastName);
        }

        foreach ($housing as $item) {
            $housing_values[$item->id] = $item->title . ' ' . $item->address;
        }

        foreach ($housing_room as $item) {
            $housing_room_values[$item->id] = $item->number;
        }

        $handbook_fields = array_keys($handbook_ids);

        foreach ($data as $item) {
            $item['user_name'] = mb_strtoupper($item['user_name']);
            $item['date_time'] = Carbon::parse($item['created_at'])->format('d.m.Y H:i');
            $item['user_role_title'] = FieldsMutation::getRoleTitle($item['user_role']);

            if ($item['model_name'] == 'Lead') {
                $item['field'] = FieldsMutation::getLeadFieldTitle($item['field_name']);
            } else {
                $item['field'] = FieldsMutation::getFieldTitle($item['field_name']);
            }

            if ($item['model_name'] == 'CandidateArrival') {
                $item['model_id'] = $item['parent_model_id'];
            } else {
                $item['model_id'] = $item['model_obj_id'];
            }

            if ($item['field_name'] == 'active') {
                $item['prev_value'] = FieldsMutation::getStatusTitle($item['prev_value']);
                $item['current_value'] = FieldsMutation::getStatusTitle($item['current_value']);
            } elseif ($item['field_name'] == 'status') {

                if ($item['model_name'] == 'Lead') {
                    $item['prev_value'] = FieldsMutation::getLeadStatusTitle($item['prev_value']);
                    $item['current_value'] = FieldsMutation::getLeadStatusTitle($item['current_value']);
                } else {
                    $item['prev_value'] = FieldsMutation::getStatusTitle($item['prev_value']);
                    $item['current_value'] = FieldsMutation::getStatusTitle($item['current_value']);
                }

                $item['prev_value'] = FieldsMutation::getArrivalStatusTitle($item['prev_value']);
                $item['current_value'] = FieldsMutation::getArrivalStatusTitle($item['current_value']);
            } elseif (in_array($item['field_name'], $handbook_fields)) {
                if (isset($handbook_values[$item['prev_value']])) {
                    $item['prev_value'] = $handbook_values[$item['prev_value']];
                }

                if (isset($handbook_values[$item['current_value']])) {
                    $item['current_value'] = $handbook_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'client_id') {
                if (isset($client_values[$item['prev_value']])) {
                    $item['prev_value'] = $client_values[$item['prev_value']];
                }

                if (isset($client_values[$item['current_value']])) {
                    $item['current_value'] = $client_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'real_vacancy_id') {
                if (isset($vacancy_values[$item['prev_value']])) {
                    $item['prev_value'] = $vacancy_values[$item['prev_value']];
                }

                if (isset($vacancy_values[$item['current_value']])) {
                    $item['current_value'] = $vacancy_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'recruiter_id') {
                if (isset($recruiter_values[$item['prev_value']])) {
                    $item['prev_value'] = $recruiter_values[$item['prev_value']];
                }

                if (isset($recruiter_values[$item['current_value']])) {
                    $item['current_value'] = $recruiter_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'housing_id') {
                if (isset($housing_values[$item['prev_value']])) {
                    $item['prev_value'] = $housing_values[$item['prev_value']];
                }

                if (isset($housing_values[$item['current_value']])) {
                    $item['current_value'] = $housing_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'housing_room_id') {
                if (isset($housing_room_values[$item['prev_value']])) {
                    $item['prev_value'] = $housing_room_values[$item['prev_value']];
                }

                if (isset($housing_room_values[$item['current_value']])) {
                    $item['current_value'] = $housing_room_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'gender') {
                if ($item['prev_value'] == 'm') {
                    $item['prev_value'] = 'Мужской';
                }
                if ($item['prev_value'] == 'f') {
                    $item['prev_value'] = 'Женский';
                }

                if ($item['current_value'] == 'm') {
                    $item['current_value'] = 'Мужской';
                }
                if ($item['current_value'] == 'f') {
                    $item['current_value'] = 'Женский';
                }
            } elseif ($item['field_name'] == 'firstName' || $item['field_name'] == 'lastName') {
                $item['prev_value'] = mb_strtoupper($item['prev_value']);
                $item['current_value'] = mb_strtoupper($item['current_value']);
            } elseif ($item['field_name'] == 'client_position_id') {
                if (isset($client_position_values[$item['prev_value']])) {
                    $item['prev_value'] = $client_position_values[$item['prev_value']];
                }

                if (isset($client_position_values[$item['current_value']])) {
                    $item['current_value'] = $client_position_values[$item['current_value']];
                }
            } elseif ($item['field_name'] == 'own_housing') {
                if ($item['prev_value'] == '0') {
                    $item['prev_value'] = 'Заселен';
                }
                if ($item['prev_value'] == '1') {
                    $item['prev_value'] = 'Выселен';
                }

                if ($item['current_value'] == '0') {
                    $item['current_value'] = 'Заселен';
                }
                if ($item['current_value'] == '1') {
                    $item['current_value'] = 'Выселен';
                }
            }

            $result[] = $item;

            if (!array_key_exists($item['user_id'], $user_ids)) {
                $user_ids[$item['user_id']] = $item['user_id'];
            }
        }

        if ($draw == 1) {
            $users = User::whereIn('id', $user_ids)->get();
        }

        return Response::json(array(
            'data' => $result,
            'draw' => (int) $draw,
            'recordsTotal' => FieldsMutation::where(function ($query) use ($id) {
                return $query->where('model_obj_id', $id)
                    ->orWhere('parent_model_id', $id);
            })->count(),
            'recordsFiltered' => $filtered_count,
            'users' => $users,
            'roles_ids' => $roles_ids,
        ), 200);
    }

    private function prepareGetJsonRequest($request, $id)
    {
        $fields = FieldsMutation::where(function ($query) use ($id) {
            return $query->where('model_obj_id', $id)
                ->orWhere('parent_model_id', $id);
        });

        if ($request->model_name && $request->model_name == 'Lead') {
            $fields = $fields->where('model_name', 'Lead');
        } else {
            $fields = $fields->where('model_name', '!=', 'Lead');
        }

        if ($request->period) {
            $fields = $fields
                ->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->period['from']))
                ->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->period['to']));
        }

        $fields = $fields->when($request->roles, function ($query, $role) {
            return $query->whereIn('user_role', $role);
        })
            ->when($request->users, function ($query, $user_ids) {
                return $query->whereIn('user_id', $user_ids);
            });

        return $fields;
    }

    public static function addLog($request, $current, $params)
    {
        if ($params == 'Candidate.New') {
            $candidate = self::getCandidateArray($current);

            foreach ($request->all() as $key => $value) {
                if (array_key_exists($key, $candidate) && $key != 'id' && !empty($value)) {
                    self::saveLog(array(
                        'model_name' => 'Candidate',
                        'model_obj_id' => $candidate['id'],
                        'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                        'field_name' => $key,
                        'prev_value' => '',
                        'current_value' => $value,
                    ));
                }
            }

            self::saveLog(array(
                'model_name' => 'Candidate',
                'model_obj_id' => $candidate['id'],
                'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                'field_name' => 'active',
                'prev_value' => null,
                'current_value' => $current->active,
            ));
        } elseif (
            $params == 'Candidate'
            || $params == 'Candidate.setStatus'
            || $params == 'Candidate.setStatus.special'
        ) {
            $candidate = self::getCandidateArray($current);

            if ($params == 'Candidate.setStatus.special') {
                self::saveLog(array(
                    'model_name' => 'Candidate',
                    'model_obj_id' => $request->id,
                    'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                    'field_name' => 'active',
                    'prev_value' => $candidate['active'],
                    'current_value' => $request->status,
                ));
            } elseif ($params == 'Candidate.setStatus') {
                self::saveLog(array(
                    'model_name' => 'Candidate',
                    'model_obj_id' => $request->id,
                    'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                    'field_name' => 'active',
                    'prev_value' => $candidate['active'],
                    'current_value' => $request->s,
                ));

                if ($request->r) {
                    self::saveLog(array(
                        'model_name' => 'Candidate',
                        'model_obj_id' => $request->id,
                        'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                        'field_name' => 'reason_reject',
                        'prev_value' => $candidate['reason_reject'],
                        'current_value' => $request->r,
                    ));
                } else {
                    self::addLog($request, $candidate, 'Candidate');
                }
            } else {
                foreach ($request->all() as $key => $value) {
                    if (array_key_exists($key, $candidate) && !empty($value) && $value != $candidate[$key]) {
                        $prev_value = null;
                        $current_value = null;

                        if ($key == 'recruiter_id') {
                            $prev_user = User::find($candidate[$key]);
                            if ($prev_user) {
                                $prev_value = $prev_user->firstName . ' ' . $prev_user->lastName;
                            }

                            $new_user = User::find($value);
                            if ($new_user) {
                                $current_value = $new_user->firstName . ' ' . $new_user->lastName;
                            }
                        } else {
                            $prev_value = $candidate[$key];
                            $current_value = $value;
                        }

                        self::saveLog(array(
                            'model_name' => 'Candidate',
                            'model_obj_id' => $request->id,
                            'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                            'field_name' => $key,
                            'prev_value' => $prev_value,
                            'current_value' => $current_value,
                        ));
                    }
                }
            }
        } elseif ($params == 'CandidateArrival' || $params == 'CandidateArrival.setStatus') {

            $arrival = self::getArrivalArr($current);

            if ($params == 'CandidateArrival.setStatus') {
                $candidate = Candidate::find($arrival['candidate_id']);

                self::saveLog(array(
                    'model_name' => 'CandidateArrival',
                    'model_obj_id' => $request->id,
                    'parent_model_id' => $arrival['candidate_id'],
                    'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                    'field_name' => 'status',
                    'prev_value' => $arrival['status'],
                    'current_value' => $request->s,
                ));
            } else {
                $candidate = Candidate::find($request->candidate_id);

                if ($candidate) {
                    foreach ($request->all() as $key => $value) {
                        if (array_key_exists($key, $arrival) && !empty($value) && $value != $arrival[$key]) {
                            self::saveLog(array(
                                'model_name' => 'CandidateArrival',
                                'model_obj_id' => $request->id,
                                'parent_model_id' => $request->candidate_id,
                                'model_data' => $candidate['firstName'] . ' ' . $candidate['lastName'],
                                'field_name' => $key,
                                'prev_value' => $arrival[$key],
                                'current_value' => $value,
                            ));
                        }
                    }
                }
            }
        }
    }

    public static function addLeadLog($req, $curr, $params)
    {
        if ($params == 'Lead.setStatus') {
            self::saveLog(array(
                'model_name' => 'Lead',
                'model_obj_id' => $req->id,
                'model_data' => $curr->name,
                'field_name' => 'status',
                'prev_value' => $curr->status,
                'current_value' => $req->status,
            ));

            if ($req->status_comment) {
                self::saveLog(array(
                    'model_name' => 'Lead',
                    'model_obj_id' => $req->id,
                    'model_data' => $curr->name,
                    'field_name' => 'status_comment',
                    'prev_value' => $curr->status_comment,
                    'current_value' => $req->status_comment,
                ));
            }
        }
    }

    public static function addFileLog($candidate, $file_type, $file_original_name, $old_original_name)
    {
        self::saveLog(array(
            'model_name' => 'Candidate',
            'model_obj_id' => $candidate->id,
            'model_data' => $candidate->firstName . ' ' . $candidate->lastName,
            'field_name' => 'file_type_' . $file_type,
            'prev_value' => $old_original_name,
            'current_value' => $file_original_name,
        ));
    }

    private static function saveLog($req)
    {
        $user = Auth::user();

        $mutated = new FieldsMutation;

        $mutated->user_id = $user->id;
        $mutated->user_role = $user->group_id;
        $mutated->user_name = $user->firstName . ' ' . $user->lastName;
        $mutated->model_name = $req['model_name'];
        $mutated->model_obj_id = $req['model_obj_id'];
        $mutated->parent_model_id = isset($req['parent_model_id']) ? $req['parent_model_id'] : null;
        $mutated->model_data = $req['model_data'];
        $mutated->field_name = $req['field_name'];
        $mutated->prev_value = $req['prev_value'];
        $mutated->current_value = $req['current_value'];

        $mutated->save();
    }

    private static function getCandidateArray($candidate)
    {
        $result = array();

        if (!is_array($candidate)) {
            $candidate = $candidate->toArray();
        }

        foreach ($candidate as $key => $value) {
            if (
                $key == 'dateOfBirth'
                || $key == 'date_arrive'
                || $key == 'date_start_work'
                || $key == 'residence_started_at'
                || $key == 'residence_stopped_at'
            ) {
                $result[$key] = $value ? Carbon::parse($value)->format('d.m.Y') : null;
            } else if (
                $key == 'logist_date_arrive'
            ) {
                $result[$key] = $value ? Carbon::parse($value)->format('d.m.Y H:i') : null;
            } else {
                if (
                    Auth::user()->isFreelancer()
                    && ($key == 'client_id' || $key == 'real_status_work_id')
                ) {
                    continue;
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    private static function getArrivalArr($arrival)
    {
        $result = array();

        foreach ($arrival->toArray() as $key => $value) {
            if ($key == 'date_arrive') {
                $result[$key] = $value ? Carbon::parse($value)->format('d.m.Y H:i') : null;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
