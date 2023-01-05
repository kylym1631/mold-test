<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use App\Services\LeadsService;
use Exception;

class CronController extends Controller
{
    public function setAuto7days()
    {
        $d = Carbon::now()->subDays(7);
        $candidates = Candidate::where('active', 9)->where('removed', false)
            ->where('date_start_work', '<=', $d)->get();

        foreach ($candidates as $candidate) {
            $candidate->worked = true;

            if ($candidate->is_payed != 1) {
                $user = User::find($candidate->user_id);
                $user->balance = $user->balance + $candidate->cost_pay;
                $user->save();

                $candidate->is_payed = 1;
            }
            $candidate->save();
        }
    }

    public function setDeclineFreelance()
    {
        $updFl = [];
        $d = Carbon::now()->subDays(60);
        $fls = User::where('activation', 1)
            ->where('fl_status', 2)
            ->with('Candidates')
            ->where('created_at', '<=', $d)
            ->get();

        foreach ($fls as $fl) {
            if (count($fl->Candidates) < 1) {
                $updFl[] = $fl->id;
            }
        }

        User::whereIn('id', $updFl)->update(['activation' => 2]);
    }

    public function importLeads()
    {
        $client = $this->getGoogleClient();
        $service = new Sheets($client);
        
        try{
            $spreadsheetId = env('GOOGLE_SHEET_ID');
            $range = 'Лиды!C2:G';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            // print_r(array_reverse($values));

            if (empty($values)) {
                print "No data found.\n";
            } else {
                $ls = new LeadsService;
                $ls->store(array_reverse($values));
            }
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    public function distributeLeadsToUsers(LeadsService $ls)
    {
        $ls->distributeToUsers();
    }

    public function resetLeadTasks(LeadsService $ls)
    {
        $ls->resetTasks();
    }

    private function getGoogleClient()
    {
        $client = new Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes('https://www.googleapis.com/auth/spreadsheets');
        $client->setAuthConfig(storage_path(env('GOOGLE_CREDENTIALS_JSON') .'.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }
}
