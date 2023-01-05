<?php

namespace App\Http\Controllers;

use App\Models\Client_contact;
use App\Models\Handbook_category;
use App\Models\User;
use App\Models\Client;
use App\Models\Handbook;
use App\Models\Handbook_client;
use App\Services\OptionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class HandbookController extends Controller
{
    public function getIndex(OptionsService $os)
    {
        $Handbook_category = Handbook_category::where('active', 1)
            ->with('Handbooks')->get();

        $options = $os->getByKeys(['min_rate_netto', 'min_rate_brutto']);

        return view('handbooks.index')
            ->with('Handbook_category', $Handbook_category)
            ->with('options', $options);
    }


    public function deleteHandbook(Request $r)
    {
        Handbook::where('id', $r->id)->update(['active' => 2]);
        return response(array('success' => "true"), 200);
    }

    public function addHandbook(Request $r)
    {
        $Handbook = new Handbook();
        $Handbook->handbook_category_id = $r->cat_id;
        $Handbook->name = $r->name;
        $Handbook->active = 1;
        $Handbook->save();

        return response(array('success' => "true"), 200);
    }


}
