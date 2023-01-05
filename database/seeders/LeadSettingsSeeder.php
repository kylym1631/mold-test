<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeadSetting;
use Carbon\Carbon;

class LeadSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(LeadSetting::find(1) == null){
            LeadSetting::insert([
                [
                    'id'=> 1,
                    'name' => 'new',
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ]);
        }
        if(LeadSetting::find(2) == null){
            LeadSetting::insert([
                [
                    'id'=> 2,
                    'name' => 'bronze',
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ]);
        }
        if(LeadSetting::find(3) == null){
            LeadSetting::insert([
                [
                    'id'=> 3,
                    'name' => 'silver',
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ]);
        }
        if(LeadSetting::find(4) == null){
            LeadSetting::insert([
                [
                    'id'=> 4,
                    'name' => 'gold',
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ]);
        }
    }
}
