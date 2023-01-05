<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\LeadStatusInform;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadStatusInformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Lead $lead)
    {
        for ($i=0; $i < 7; $i++) {
            $NOW = Carbon::now();

            if(LeadStatusInform::find($i + 1) == null){
                LeadStatusInform::insert([
                    [
                        'id'=> $i + 1,
                        'system_id'=> $i,
                        'name' => $lead->getStatusTitle($i),
                        'info'=>'',
                        'created_at'=>$NOW,
                        'updated_at'=>$NOW,
                    ],
                ]);
            }
        }
    }
}
