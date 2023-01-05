<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Services\CandidatesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        }, Candidate::allowedStatusesToEmployeeView());

        return view('employees.index')->with('filters', $filters);
    }

    public function listJson(Request $req, CandidatesService $cs)
    {
        $draw = request()->get('draw');
        $view = request()->get('view');
        
        $list = $cs->getList($req, 'employee');
        $data = $cs->getResultData($list['data'], 'employee', $view);

        return response()->json([
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $list['filtered_count'],
            'recordsFiltered' => $list['filtered_count'],
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
