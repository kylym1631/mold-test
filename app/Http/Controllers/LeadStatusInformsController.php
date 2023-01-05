<?php

namespace App\Http\Controllers;

use App\Models\LeadStatusInform;
use Illuminate\Http\Request;

class LeadStatusInformsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('leads.status-inform');
    }

    public function listJson()
    {
        $draw = request()->get('draw');

        $items = LeadStatusInform::all();

        return response()->json([
            'data' => $items,
            'draw' => $draw,
            'recordsTotal' => count($items),
            'recordsFiltered' => count($items),
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
        $item = LeadStatusInform::where('system_id', $id)->first();
        
        return response()->json([ 'data' => $item ], 200);
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
        $item = LeadStatusInform::where('system_id', $id)->first();

        $item->info = $request->info;
        
        $item->save();
        
        return response()->json(['success' => 'true'], 200);
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
