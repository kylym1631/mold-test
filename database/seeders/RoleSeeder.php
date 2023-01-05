<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        for ($i=1; $i <= 14; $i++) {
            $NOW = Carbon::now();

            if(Role::find($i) == null){
                Role::insert([
                    [
                        'id'=> $i,
                        'name' => $user->getRoleTitle($i),
                        'active'=>1,
                        'created_at'=>$NOW,
                        'updated_at'=>$NOW,
                    ],
                ]);
            }
        }
        
    }
}
