<?php

namespace App\Http\Controllers;

use App\Models\CandidateDocument;
use App\Models\Template;
use App\Services\CandidatesService;
use App\Services\TemplatesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('templates.index');
    }

    public function listJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");
        $active = request()->get("active");
        $users = request()->get("users");
        $search = request()->get("search");

        $filtered_count = $this->prepareGetJsonRequest($active, $users, $search);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest($active, $users, $search);
        $items = $items->orderBy('id', 'desc');

        $items = $items
            ->skip($start)
            ->take($rowperpage)
            ->get();

        return response()->json([
            'data' => $items,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetJsonRequest($active, $users, $search)
    {
        $items = Template::query();

        if ($active != '') {
            $items = $items->where('active', $active);
        }

        if ($users != '') {
            $items = $items->whereIn('user_id', $users);
        }

        $items = $items->when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', '%' . $search . '%');
        });

        return $items;
    }

    public function itemJson($id)
    {
        $item = Template::find($id);

        if ($item) {
            return response()->json($item->toArray(), 200);
        } else {
            return response()->json(['success' => 'false', 'error' => 'Шаблон не найден'], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('templates.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required',
        ], [], [
            'title' => '«Название»',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $item = null;

        if ($req->has('id')) {
            $item = Template::find($req->id);
        }

        if (!$item) {
            $item = new Template;
        }

        $item->user_id = Auth::user()->id;
        $item->type = $req->type ?: null;
        $item->title = $req->title ?: null;
        $item->description = $req->description ?: null;
        $item->tpl_head = $req->tpl_head ? json_encode($req->tpl_head) : null;
        $item->tpl_body = $req->tpl_body ? json_encode($req->tpl_body) : null;
        $item->tpl_foot = $req->tpl_foot ? json_encode($req->tpl_foot) : null;
        $item->options = $req->options ?: null;

        $item->save();

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
        return view('templates.view')->with('id', $id);
    }

    public function docPreview(Request $req, TemplatesService $t_srv, CandidatesService $cnd_srv)
    {
        $docs_html = [];
        $store_docs = [];

        if ($req->has('candidate_id')) {
            $docs = $t_srv->makeCandidateDoc($req->tpl_id, $req->candidate_id);

            foreach ($docs as $item) {
                $store_docs[] = [
                    'html' => $item['html'],
                    'title' => $item['tpl']['title'],
                ];

                $docs_html[] = [
                    'html' => $item['html'],
                    'tpl_id' => $item['tpl']['id'],
                ];
            }

            $cnd_srv->storeDocuments($store_docs, $req->candidate_id);
        } else
        if ($req->has('doc_id')) {
            $doc = CandidateDocument::find($req->doc_id);

            $docs_html[] = [
                'html' => json_decode($doc->document),
            ];
        }

        return view('templates.preview')->with('docs_html', $docs_html);
    }

    public function makePdf(Request $req, TemplatesService $t_srv)
    {
        $docs_html = [];

        if ($req->has('candidate_id')) {

            $docs = $t_srv->makeCandidateDoc([request()->query('tpl_id')], $req->candidate_id);

            $t_srv->pdf($docs[0]['html']);
        } else 
        if ($req->has('doc_id')) {
            $doc = CandidateDocument::find($req->doc_id);
            $t_srv->pdf(json_decode($doc->document));
            $docs_html[] = json_decode($doc->document);
        }

        return view('templates.preview')->with('docs_html', $docs_html);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('templates.edit')->with('id', $id);
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
