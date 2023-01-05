<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\Task;
use App\Models\LeadSetting;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\CandidateController;
use App\Models\Handbook;

class LeadsService
{
    public $import_columns = [
        'date' => '0',
        'company' => '1',
        'name' => '2',
        'phone' => '3',
        'viber' => '4',
    ];

    public function prepareGetJsonRequest($search, $period, $recruiter, $company, $status, $speciality, $last_action_at)
    {
        $users = Lead::query();

        if ($period) {
            $users = $users
                ->whereDate('date', '>=', Carbon::createFromFormat('Y-m-d', $period['from']))
                ->whereDate('date', '<=', Carbon::createFromFormat('Y-m-d', $period['to']));
        }

        if ($last_action_at) {
            $users = $users
                ->whereDate('last_action_at', '>=', Carbon::createFromFormat('Y-m-d', $last_action_at['from']))
                ->whereDate('last_action_at', '<=', Carbon::createFromFormat('Y-m-d', $last_action_at['to']));
        }

        if ($recruiter) {
            $users = $users->whereIn('user_id', $recruiter);
        }

        if ($company) {
            $users = $users->whereIn('company', $company);
        }

        if ($speciality) {
            $without_spec = Handbook::where('handbook_category_id', 13)->first();

            if (in_array($without_spec->id, $speciality)) {
                $users = $users->where(function ($q) use ($speciality) {
                    $q->whereIn('speciality_id', $speciality)
                        ->orWhereNull('speciality_id');
                });
            } else {
                $users = $users->whereIn('speciality_id', $speciality);
            }
        }

        if ($status) {
            $users = $users->where(function ($q) use ($status) {
                foreach ($status as $st_item) {
                    if (stripos($st_item, 'archive') !== false) {
                        $arch_arr = explode('.', $st_item);

                        $q->orWhere(function ($qr) use ($arch_arr) {
                            $qr->where('active', false)
                                ->where('status_comment', $arch_arr[1]);
                        });
                    }

                    if (stripos($st_item, '3.') !== false) {
                        $cfc_arr = explode('.', $st_item);

                        $q->orWhere(function ($qr) use ($cfc_arr) {
                            $qr->where('status', 3)
                                ->where('active', true)
                                ->where('count_failed_call', $cfc_arr[1]);
                        });
                    }
                }

                if (in_array('0', $status)) {
                    $q->orWhere(function ($qr) {
                        $qr->where('active', true)
                            ->whereNull('status');
                    });
                }

                $q->orWhere(function ($qr) use ($status) {
                    $qr->where('active', true)
                        ->whereIn('status', $status);
                });
            });
        }

        $users = $users->when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('company', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%')
                ->orWhere('viber', 'LIKE', '%' . $search . '%');
        });

        return $users;
    }

    public function store($data, $manual_import = false)
    {
        $newData = [];
        $last_data_count = 0;
        $last_lead = null;
        $import_to_last_lead = true;

        if (!$manual_import) {
            $last_data_count_opt = Option::where('key', 'last_leads_import_data_count')->first();
            $cur_data_count = count($data);

            if ($last_data_count_opt) {
                $last_data_count = (int) $last_data_count_opt->value;

                if ($cur_data_count > $last_data_count) {
                    $data = array_slice($data, 0, $cur_data_count - $last_data_count);
                    $import_to_last_lead = false;
                }

                $last_data_count_opt->value = $cur_data_count;
                $last_data_count_opt->save();
            } else {
                Option::create([
                    'key' => 'last_leads_import_data_count',
                    'value' => $cur_data_count,
                ]);
            }

            if ($import_to_last_lead) {
                $last_lead = Lead::orderBy('id', 'DESC')->first();
            }
        }

        if ($data) {
            foreach ($data as $k => $item) {
                if (!$item || !isset($item[3])) {
                    continue;
                }


                $date = null;

                try {
                    $date = isset($item[0]) ? Carbon::parse($item[0]) : Carbon::now();

                    if ($date < Carbon::createFromFormat('Y-m-d H:i', '2022-09-01 00:00')) {
                        continue;
                    }
                } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                    if ($e) {
                        $date = Carbon::now();
                    }
                }

                $phone = preg_replace('/\D/i', '', $item[3]);

                if ($import_to_last_lead && $last_lead && $phone == $last_lead->phone) {
                    break;
                }

                $this_is_duplicate = false;

                if ($newData) {
                    foreach ($newData as $n_key => $n_item) {
                        if ($n_item['phone'] == $phone) {
                            if ($n_item['date'] <= $date) {
                                $newData[$n_key]['active'] = 0;
                                $newData[$n_key]['status_comment'] = 'Дубликат';
                            } else {
                                $this_is_duplicate = true;
                            }
                        }
                    }
                }

                $newData[] = [
                    'date' => $date,
                    'source' => 'Facebook',
                    'company' => isset($item[1]) ? trim($item[1]) : null,
                    'name' => isset($item[2]) ? $item[2] : null,
                    'phone' => $phone,
                    'viber' => isset($item[4]) ? $item[4] : null,
                    'active' => $this_is_duplicate ? 0 : 1,
                    'status_comment' => $this_is_duplicate ? 'Дубликат' : '',
                ];
            }
        }

        $insertData = [];
        $to_duplicate_ids = [];

        if ($newData) {
            $phones = [];

            foreach ($newData as $item) {
                $phones[] = $item['phone'];
            }

            $same_items = Lead::whereIn('phone', $phones)
                ->where('active', true)
                ->orderBy('id', 'DESC')
                ->with('Contacts')
                ->get();

            if (count($same_items) > 0) {
                foreach ($newData as $new_item) {
                    if ($new_item['active'] == 0) {
                        $insertData[] = $new_item;
                        continue;
                    }

                    foreach ($same_items as $same_item) {
                        if ($same_item->phone == $new_item['phone']) {
                            if (
                                $same_item->candidate_id
                                || $same_item->user_id
                                || $same_item->speciality_id
                                || $same_item->status !== null
                                || count($same_item->Contacts) > 0
                            ) {

                                Lead::find($same_item->id)
                                    ->update([
                                        'date' => $new_item['date'],
                                        'company' => $new_item['company'],
                                        'name' => $new_item['name'],
                                        'viber' => $new_item['viber'],
                                        // 'status' => null,
                                    ]);

                                $new_item['active'] = 0;
                                $new_item['status_comment'] = 'Дубликат';
                                $new_item['date'] = $same_item->date;
                                $new_item['company'] = $same_item->company;
                                $new_item['name'] = $same_item->name;
                                $new_item['viber'] = $same_item->viber;
                            } else {
                                $to_duplicate_ids[] = $same_item->id;
                            }
                        }
                    }

                    $insertData[] = $new_item;
                }
            } else {
                $insertData = $newData;
            }
        }

        if ($insertData) {
            if ($to_duplicate_ids) {
                Lead::whereIn('id', $to_duplicate_ids)
                    ->update([
                        'active' => 0,
                        'status_comment' => 'Дубликат',
                    ]);
            }

            DB::table('leads')->insert(array_reverse($insertData));
        }
    }

    public function distributeToUsers($toSameUser = false, $test = false)
    {
        $test_data = [
            'free_users' => [],
            'leads_a' => [],
            'leads_b' => [],
        ];

        if ($toSameUser) {
            $user = User::where('id', Auth::user()->id)
                ->where('group_id', 2)
                ->with([
                    'LeadsSettings',
                    'RecruitmentDirector',
                    'Tasks' => function ($query) {
                        $query->whereIn('status', [1, 3])
                            ->where(function ($qr) {
                                $qr->where('type', 21)
                                    ->orWhere(function ($q) {
                                        $q->where('type', 23)
                                            ->where('start', '<', Carbon::now());
                                    });
                            });
                    },
                    'LeadsStatusesHistory' => function ($q) {
                        $q->whereIn('current_value', ['2', '3', '7']);
                    },
                ])
                ->first();
        } else {
            $users = User::where('activation', 1)
                ->where('group_id', 2)
                ->whereNotNull('was_online_at')
                ->where('was_online_at', '>', Carbon::now()->subMinutes(10))
                ->with([
                    'LeadsSettings',
                    'RecruitmentDirector',
                    'Tasks' => function ($query) {
                        $query->whereIn('status', [1, 3])
                            ->where(function ($qr) {
                                $qr->where('type', 21)
                                    ->orWhere(function ($q) {
                                        $q->where('type', 23)
                                            ->where('start', '<', Carbon::now());
                                    });
                            });
                    },
                    'LeadsStatusesHistory' => function ($q) {
                        $q->whereIn('current_value', ['2', '3', '7']);
                    },
                ])
                ->get();
        }

        $free_users = array();

        if ($toSameUser) {
            if (count($user->Tasks) == 0 && $user->LeadsSettings && count($user->LeadsSettings) > 0) {
                $free_users[] = $user;
            }
        } else {
            foreach ($users as $user) {
                if (count($user->Tasks) == 0 && $user->LeadsSettings && count($user->LeadsSettings) > 0) {
                    $free_users[] = $user;
                }
            }
        }

        $fast_exclude_ids = [];

        $without_spec = Handbook::where('handbook_category_id', 13)->first();

        foreach ($free_users as $user) {
            $LeadsSettingsBlocks = [];

            if ($user->LeadsSettings) {
                $leads_settings = [];

                foreach ($user->LeadsSettings as $LeadsSettings) {
                    $leads_settings[] = $LeadsSettings->value;
                }

                $leads_settings = LeadSetting::whereIn('id', $leads_settings)->get();

                if ($leads_settings) {
                    foreach ($leads_settings as $set_key => $l_set) {
                        $LeadsCompanies = [];
                        $LeadsStatuses = [];
                        $LeadsSpeciality = [];
                        $LeadsDate = '';

                        if ($l_set->sources) {
                            $LeadsCompanies = json_decode($l_set->sources);
                        }

                        if ($l_set->statuses) {
                            $LeadsStatuses = json_decode($l_set->statuses);
                        }

                        if ($l_set->speciality) {
                            $LeadsSpeciality = json_decode($l_set->speciality);
                        }

                        if ($l_set->lifetime_days != null) {
                            $LeadsDate = Carbon::now()->subDays($l_set->lifetime_days)->startOfDay();
                        } else {
                            $LeadsDate = Carbon::createFromDate(1991, 1, 1)->startOfDay();
                        }

                        $LeadsSettingsBlocks[] = [
                            'LeadsCompanies' => $LeadsCompanies,
                            'LeadsStatuses' => $LeadsStatuses,
                            'LeadsSpeciality' => $LeadsSpeciality,
                            'LeadsDate' => $LeadsDate,
                        ];
                    }
                }
            }

            if (!$LeadsSettingsBlocks) {
                return false;
            }

            $ExcludedLeads = [];

            if ($user->LeadsStatusesHistory) {
                foreach ($user->LeadsStatusesHistory as $LeadsStatusesHistory) {
                    $ExcludedLeads[] = $LeadsStatusesHistory->model_obj_id;
                }
            }

            $lead = Lead::whereNull('user_id')
                ->whereNotIn('id', $fast_exclude_ids)
                ->where('active', true)
                ->where(function ($query) use ($ExcludedLeads) {
                    $query->whereNull('status')
                        ->orWhere(function ($q) use ($ExcludedLeads) {
                            $q->where('status', 2)
                                ->whereNotIn('id', $ExcludedLeads);
                        })
                        ->orWhere(function ($q) {
                            $q->where('status', 1)
                                ->where(function ($qy) {
                                    $qy->where(function ($qry) {
                                        $qry->where('last_action_at', '<', Carbon::now()->subHours(24))
                                            ->where('count_liquidity', 0);
                                    })
                                        ->orWhere(function ($qry) {
                                            $qry->where('last_action_at', '<', Carbon::now()->subHours(24))
                                                ->where('count_liquidity', 1);
                                        })
                                        ->orWhere(function ($qry) {
                                            $qry->where('last_action_at', '<', Carbon::now()->subHours(24))
                                                ->where('count_liquidity', 2);
                                        });
                                });
                        })
                        ->orWhere(function ($q) use ($ExcludedLeads) {
                            $q->where('status', 3)
                                ->where(function ($qy) use ($ExcludedLeads) {
                                    $qy->where(function ($qry) use ($ExcludedLeads) {
                                        $qry->whereIn('count_failed_call', [1])
                                            ->whereNotIn('id', $ExcludedLeads);
                                    })
                                        ->orWhere(function ($qry) use ($ExcludedLeads) {
                                            $qry->whereIn('count_failed_call', [3])
                                                ->where('last_action_at', '<', Carbon::now()->subMinutes(60))
                                                ->whereNotIn('id', $ExcludedLeads);
                                        })
                                        ->orWhere(function ($qry) {
                                            $qry->where('last_action_at', '<', Carbon::now()->subMinutes(15))
                                                ->whereIn('count_failed_call', [2]);
                                        });
                                });
                        })
                        ->orWhere(function ($q) use ($ExcludedLeads) {
                            $q->where('status', 7)
                                ->where('last_action_at', '<', Carbon::now()->subDays(3))
                                ->whereNotIn('id', $ExcludedLeads);
                        });
                });

            $lead = $this->filterLeadsBySettings($lead, $LeadsSettingsBlocks, $without_spec);

            $lead = $lead
                ->orderBy('status', 'ASC')
                ->orderBy('count_failed_call', 'ASC')
                ->orderBy('date', 'DESC')
                ->first();

            // 1 failed call lead
            // $one_failed_call_lead = Lead::whereNull('user_id')
            //     ->whereNotIn('id', $fast_exclude_ids)
            //     ->where('active', true)
            //     ->where('status', 3)
            //     ->where('count_failed_call', 1)
            //     // ->where('last_action_at', '<', Carbon::now()->subMinutes(60))
            //     ->whereNotIn('id', $ExcludedLeads);

            // $one_failed_call_lead = $this->filterLeadsBySettings($one_failed_call_lead, $LeadsSettingsBlocks, $without_spec);

            // $one_failed_call_lead = $one_failed_call_lead
            //     ->orderBy('date', 'DESC')
            //     ->first();

            // if (
            //     $one_failed_call_lead
            //     && !in_array($one_failed_call_lead->id, $fast_exclude_ids)
            //     && $lead
            //     && $one_failed_call_lead->date > $lead->date
            // ) {
            //     $fast_exclude_ids[] = $one_failed_call_lead->id;

            //     $one_failed_call_lead->user_id = $user->id;
            //     $one_failed_call_lead->save();

            //     $task = new Task;
            //     $task->start = Carbon::now();
            //     $task->autor_id = $user->RecruitmentDirector ? $user->RecruitmentDirector->id : null;
            //     $task->to_user_id = $user->id;
            //     $task->status = 1;
            //     $task->type = 21;
            //     $task->title = Task::getTypeTitle($task->type);
            //     $task->lead_id = $one_failed_call_lead->id;
            //     $task->save();
            // } else

            if ($test) {
                $test_data['leads_a'][] = [
                    'id' => $lead['id'],
                    'status' => $lead['status'],
                    'user_id' => $user['id'],
                ];
            }

            if (
                $lead
                && !in_array($lead->id, $fast_exclude_ids)
            ) {
                $fast_exclude_ids[] = $lead->id;

                if ($test === false) {
                    $lead->user_id = $user->id;
                    $lead->save();

                    $task = new Task;
                    $task->start = Carbon::now();
                    $task->autor_id = $user->RecruitmentDirector ? $user->RecruitmentDirector->id : null;
                    $task->to_user_id = $user->id;
                    $task->status = 1;
                    $task->type = 21;
                    $task->title = Task::getTypeTitle($task->type);
                    $task->lead_id = $lead->id;
                    $task->save();
                } else {
                    $test_data['leads_b'][] = [
                        'id' => $lead['id'],
                        'status' => $lead['status'],
                        'user_id' => $user['id'],
                    ];
                }
            }
        }

        if ($test) {
            $test_data['free_users'] = array_map(function ($u) {
                return [
                    'id' => $u['id'],
                ];
            }, $free_users) ;

            dd($test_data);
        }
    }

    private function filterLeadsBySettings($lead, $LeadsSettingsBlocks, $without_spec)
    {
        $lead = $lead->where(function ($query) use ($LeadsSettingsBlocks, $without_spec) {

            foreach ($LeadsSettingsBlocks as $set_block) {

                $query = $query->orWhere(function ($qry) use ($set_block, $without_spec) {
                    if (in_array('Без компании', $set_block['LeadsCompanies'])) {
                        $qry = $qry->where(function ($q) use ($set_block) {
                            $q->whereIn('company', $set_block['LeadsCompanies'])
                                ->orWhere('company', '')
                                ->orWhereNull('company');
                        });
                    } else {
                        $qry = $qry->whereIn('company', $set_block['LeadsCompanies']);
                    }

                    if (in_array('0', $set_block['LeadsStatuses'])) {
                        $qry = $qry->where(function ($q) use ($set_block) {
                            $q->whereIn('status', $set_block['LeadsStatuses'])
                                ->orWhereNull('status');
                        });
                    } else {
                        $qry = $qry->whereIn('status', $set_block['LeadsStatuses']);
                    }

                    if (in_array($without_spec->id, $set_block['LeadsSpeciality'])) {
                        $qry = $qry->where(function ($q) use ($set_block) {
                            $q->whereIn('speciality_id', $set_block['LeadsSpeciality'])
                                ->orWhereNull('speciality_id');
                        });
                    } else {
                        $qry = $qry->whereIn('speciality_id', $set_block['LeadsSpeciality']);
                    }

                    if ($set_block['LeadsDate']) {
                        $qry = $qry->whereDate('date', '>=', $set_block['LeadsDate']);
                    }
                });
            }
        });

        return $lead;
    }

    public function resetTasks()
    {
        $tasks = Task::where('type', 21)
            ->whereIn('status', [1, 3])
            ->where('created_at', '<', Carbon::now()->subHours(1))
            ->get();

        foreach ($tasks as $task) {
            $lead = Lead::find($task->lead_id);

            if (!$lead->candidate_id) {
                $lead->user_id = null;
                $lead->save();
            }

            $task->status = 2;
            $task->save();
        }
    }

    // public function resetLeadTasksForUser($user_id)
    // {
    // $tasks = Task::where('type', 21)
    //     ->where('to_user_id', $user_id)
    //     ->whereIn('status', [1,3])
    //     ->get();

    // if ($tasks) {
    //     foreach ($tasks as $task) {
    //         $lead = Lead::find($task->lead_id);

    //         if ($lead) {
    //             $lead->user_id = null;
    //             $lead->save();
    //         }

    //         $task->status = 2;
    //         $task->save();
    //     }
    // }
    // }

    public function createCandidate($l_id)
    {
        $lead = Lead::find($l_id);

        $name = $lead->name ? explode(' ', $lead->name) : ['Имя', 'Фамилия'];

        $addReq = new Request;
        $addReq->setMethod('POST');
        $addReq->request->add([
            'firstName' => $name[0],
            'lastName' => isset($name[1]) ? $name[1] : $name[0],
            'phone' => '+' . trim($lead->phone),
            'viber' => !empty($lead->viber) ? '+' . $lead->viber : '',
            'speciality_id' => $lead->speciality_id,
        ]);
        $addReq->createFromLead = true;

        $cCtrl = new CandidateController;
        $cResp = $cCtrl->postAdd($addReq);

        if (isset($cResp->original) && $cResp->original['success'] == 'false') {
            return $cResp;
        } else {
            $lead->candidate_id = $cResp['candidate_id'];
            $lead->status_comment = 'Кандидат';
            $lead->active = 0;
            $lead->save();
            return $cResp;
        }
    }
}
