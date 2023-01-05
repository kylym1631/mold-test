<?php

namespace App\Http\Controllers;

use App\Models\CandidateLegalisation;
use App\Services\FilesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class LegalisationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $items = CandidateLegalisation::where('candidate_id', $req->candidate_id)
            ->with('Files')
            ->with('Type')
            ->orderBy('date_to', 'DESC')
            ->get();

        $data = array_map(function ($item) {
            $item['date_from'] = $item['date_from']
                ? Carbon::parse($item['date_from'])->format('d.m.Y')
                : '';

            $item['date_to'] = $item['date_to']
                ? Carbon::parse($item['date_to'])->format('d.m.Y')
                : '';

            $item['issue_date'] = $item['issue_date']
                ? Carbon::parse($item['issue_date'])->format('d.m.Y')
                : '';

            return $item;
        }, $items->toArray());

        return response()->json([
            'data' => $data,
            'draw' => $req->draw,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req, FilesService $FS)
    {
        $validator = Validator::make($req->all(), [
            'doc_type_id' => 'required|numeric',
            'file' => 'required',
            'file.*' => [
                'required',
                File::types(['jpeg', 'jpg', 'png', 'pdf'])->max(5 * 1024),
            ],
        ], [], [
            'doc_type_id' => '«Тип документа»',
            'file' => '«Файл документа»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = new CandidateLegalisation;

        $item->candidate_id = $req->candidate_id;
        $item->doc_type_id = $req->doc_type_id;

        $item->title = $req->title ?: null;

        $item->date_from = $req->date_from
            ? Carbon::createFromFormat('Y-m-d', $req->date_from)->startOfDay()
            : null;

        $item->date_to = $req->date_to
            ? Carbon::createFromFormat('Y-m-d', $req->date_to)->startOfDay()
            : null;

        $item->issue_date = $req->issue_date
            ? Carbon::createFromFormat('Y-m-d', $req->issue_date)->startOfDay()
            : null;

        $item->who_issued = $req->who_issued ?: null;
        $item->number = $req->number ?: null;

        $item->save();

        $f_check = $FS->checkFiles($req);

        if ($f_check && $f_check['success'] == 'false') {
            return response()->json($f_check, 200);
        }

        $FS->addFiles($req, 'candidate_legalisation_id', $item->id);

        return response()->json(['success' => 'true'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = CandidateLegalisation::where('id', $id)
            ->with('Files')
            ->with('Type')
            ->first();

        if ($item) {
            $item->date_from = $item->date_from
                ? Carbon::parse($item->date_from)->format('Y-m-d')
                : '';

            $item->date_to = $item->date_to
                ? Carbon::parse($item->date_to)->format('Y-m-d')
                : '';

            $item->issue_date = $item->issue_date
                ? Carbon::parse($item->issue_date)->format('Y-m-d')
                : '';
        }

        return response()->json($item, 200);
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
    public function update(Request $req, FilesService $FS, $id)
    {
        $validator = Validator::make($req->all(), [
            'doc_type_id' => 'required|numeric',
        ], [], [
            'doc_type_id' => '«Тип документа»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        if (empty($req->extistFiles)) {
            $validator = Validator::make($req->all(), [
                'file' => 'required',
                'file.*' => [
                    'required',
                    File::types(['jpeg', 'jpg', 'png', 'pdf'])->max(5 * 1024),
                ],
            ], [], [
                'file' => '«Файл документа»',
            ]);
    
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response(array('success' => "false", 'error' => $error), 200);
            }
        }

        $item = CandidateLegalisation::find($id);

        $item->candidate_id = $req->candidate_id;
        $item->doc_type_id = $req->doc_type_id;

        $item->title = $req->title ?: null;

        $item->date_from = $req->date_from
            ? Carbon::createFromFormat('Y-m-d', $req->date_from)->startOfDay()
            : null;

        $item->date_to = $req->date_to
            ? Carbon::createFromFormat('Y-m-d', $req->date_to)->startOfDay()
            : null;

        $item->issue_date = $req->issue_date
            ? Carbon::createFromFormat('Y-m-d', $req->issue_date)->startOfDay()
            : null;

        $item->who_issued = $req->who_issued ?: null;
        $item->number = $req->number ?: null;

        $item->save();

        if ($req->file) {
            $f_check = $FS->checkFiles($req);

            if ($f_check && $f_check['success'] == 'false') {
                return response()->json($f_check, 200);
            }

            $FS->addFiles($req, 'candidate_legalisation_id', $item->id);
        }

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
