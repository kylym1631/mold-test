<?php

use App\Imports\CandidatesImport;
use App\Models\Candidate;
use App\Models\CandidateHousing;
use App\Models\CandidatePosition;
use App\Models\Client_position;
use App\Models\ClientPositionRate;
use App\Models\Lead;
use App\Models\LeadSetting;
use App\Models\RolePermission;
use App\Services\LeadsService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('set-candidates-positions', function () {
//     $cnds = Candidate::with('ClientPosition')->get();

//     foreach ($cnds as $cnd) {
//         if (!$cnd->ClientPosition) {
//             $c = Candidate::find($cnd->id);
//             $c->client_position_id = null;
//             $c->save();
//         }
//     }

//     $can_pos = CandidatePosition::with('Position')->get();

//     foreach ($can_pos as $pos) {
//         if (!$pos->Position) {
//             CandidatePosition::where('id', $pos->id)->delete();
//         }
//     }

//     $exists_ids = CandidatePosition::pluck('candidate_id');

//     $cnds = Candidate::whereNotIn('id', $exists_ids)
//         ->whereNotNull('client_position_id')
//         ->whereNotNull('date_start_work')
//         ->get();

//     $data = [];

//     $NOW = Carbon::now();

//     foreach ($cnds as $cnd) {
//         $data[] = [
//             'candidate_id' => $cnd->id,
//             'client_position_id' => $cnd->client_position_id,
//             'start_at' => $cnd->date_start_work,
//             'created_at' => $NOW,
//             'updated_at' => $NOW,
//         ];
//     }

//     CandidatePosition::insert($data);
// })->purpose('Set Candidates Positions');

// Artisan::command('create-positions-rates', function () {
//     $pos_items = Client_position::all();

//     $data = [];

//     $NOW = Carbon::now();

//     foreach ($pos_items as $pos) {
//         $data[] = [
//             'client_position_id' => $pos->id,
//             'type' => 'rate',
//             'start_at' => $pos->created_at,
//             'amount' => $pos->rate,
//             'created_at' => $NOW,
//             'updated_at' => $NOW,
//         ];

//         $data[] = [
//             'client_position_id' => $pos->id,
//             'type' => 'rate_after',
//             'start_at' => $pos->created_at,
//             'amount' => $pos->rate_after,
//             'created_at' => $NOW,
//             'updated_at' => $NOW,
//         ];

//         $data[] = [
//             'client_position_id' => $pos->id,
//             'type' => 'personal_rate',
//             'start_at' => $pos->created_at,
//             'amount' => $pos->personal_rate,
//             'created_at' => $NOW,
//             'updated_at' => $NOW,
//         ];
//     }

//     ClientPositionRate::insert($data);
// })->purpose('Move Old Positions Rates to New entity');

// Artisan::command('set-candidates-housing', function () {

//     $exists_ids = CandidateHousing::pluck('candidate_id');

//     $cnds = Candidate::whereNotIn('id', $exists_ids)
//         ->whereNotNull('housing_id')
//         ->whereNotNull('residence_started_at')
//         ->get();

//     $data = [];

//     $NOW = Carbon::now();

//     foreach ($cnds as $cnd) {
//         $data[] = [
//             'candidate_id' => $cnd->id,
//             'housing_id' => $cnd->housing_id,
//             'housing_room_id' => $cnd->housing_room_id,
//             'start_at' => $cnd->residence_started_at,
//             'end_at' => $cnd->residence_stopped_at,
//             'created_at' => $NOW,
//             'updated_at' => $NOW,
//         ];
//     }

//     CandidateHousing::insert($data);
// })->purpose('Set First Candidates Housing');

// Artisan::command('refactor-permissions', function () {

//     $all = RolePermission::all();

//     foreach ($all as $perm) {
//         $alias = $perm->alias;

//         if (stripos($alias, 'userRole') !== false) {
//             RolePermission::find($perm->id)->delete();
//         } else
//         if (stripos($alias, '.') === false) {
//             $rp = RolePermission::find($perm->id);
//             $rp->alias = $alias . '.view';
//             $rp->save();
//         }
//     }
// })->purpose('Refactor Permissions');

// Artisan::command('import-candidates', function () {
//     // Excel::import(new CandidatesImport, storage_path('imports/the_candidates.ods'));
// })->purpose('Import Candidates');

// Artisan::command('candidates-leads-to-archive', function () {

//     Lead::whereNotNull('candidate_id')->update([
//         'status_comment' => 'Кандидат',
//         'active' => 0,
//     ]);
// })->purpose('candidates leads to an archive');

// Artisan::command('reset-duplicated-leads-status', function () {

//     $items = Lead::whereNotNull('last_action_at')
//         ->whereNull('status')
//         ->with(['FieldsMutations' => function ($q) {
//             $q->where('field_name', 'status')->orderBy('id', 'DESC');
//         }])
//         ->get();

//     foreach ($items as $item) {
//         if (count($item->FieldsMutations)) {
//             $item->status = $item->FieldsMutations[0]->current_value;
//             $item->save();
//         }
//     }
// })->purpose('reset duplicated leads status');

// Artisan::command('set-default-leads-settings', function () {

//     LeadSetting::whereNull('delays')->update([
//         'delays' => '{"failed_call_1_delay":"60","failed_call_2_delay":"360","failed_call_3_delay":"60","not_interested_delay":"1440","not_liquidity_delay":"1440","liquidity_delay":"1440"}',
//     ]);
// });

Artisan::command('check-leads-distribute', function (LeadsService $ls) {
    $ls->distributeToUsers(false, true);
});

// Artisan::command('clear-leads-table', function () {
//     Lead::where('active', false)
//         ->whereNull('candidate_id')
//         ->whereNot('status_comment', 'Не заинтересован')
//         ->delete();

//     Lead::where('status', 3)
//         ->whereIn('count_failed_call', [4, 5, 6, 7, 8, 9, 10, 11, 12])
//         ->whereNull('user_id')
//         ->delete();
// });
