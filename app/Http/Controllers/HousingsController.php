<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Housing;
use App\Models\Housing_room;
use App\Models\Housing_contact;
use App\Models\C_file;
use App\Models\Housing_client;
use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HousingsController extends Controller
{
    public function getIndex()
    {
        return view('housing.index');
    }

    public function getJson()
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");
        $status = request('status');
        $search = request('search');
        $period = request('period');
        $client = request('client');
        $status = request('status');

        $filtered_count = $this->prepareGetJsonRequest($search, $status, $client);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetJsonRequest($search, $status, $client);
        $items = $items->orderBy('id', 'desc');

        $items = $items
            ->with(['Housing_client.Client'])
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($items as $key => $item) {
            $clients = [];

            if ($item->Housing_client != null) {
                foreach ($item->Housing_client as $Client) {
                    $clients[] = $Client->Client;
                }
            }

            $item->title = $item->title . ' ' . $item->address;

            $data[$key] = $item;
            $data[$key]['clients'] = $clients;
        }

        return response()->json([
            'data' => array_values($data),
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetJsonRequest($search, $status, $client)
    {
        $items = Housing::query();

        if ($status != '') {
            $items = $items->where('active', $status);
        }

        if ($client != '') {
            $items = $items->whereHas('Housing_client', function ($q) use ($client) {
                $q->whereIn('client_id', $client);
            });
        }

        if (Auth::user()->isKoordinator()) {
            $clients_ids = Client::where('coordinator_id', Auth::user()->id)
                ->where('active', true)
                ->pluck('id');

            $items = $items->whereHas('Housing_client', function ($q) use ($clients_ids) {
                $q->whereIn('client_id', $clients_ids);
            });
        }

        $items = $items->when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('address', 'LIKE', '%' . $search . '%');
        });

        return $items;
    }

    public function viewIndex(Request $req)
    {
        $item = null;

        if ($req->has('id')) {
            $item = Housing::with('Housing_client')
                ->with('Housing_client.Client')
                ->with('Housing_contact')
                ->with('Gallery')
                ->find($req->id);

            $gallery_files = [];

            if ($item->Gallery != null) {
                foreach ($item->Gallery as $GalleryItem) {
                    $gallery_files[] = [
                        'path' => $GalleryItem->path,
                        'name' => $GalleryItem->original_name
                    ];
                }
            }

            $item->gallery_files = $gallery_files;

            $contacts = [];

            if ($item->Housing_contact != null) {
                foreach ($item->Housing_contact as $HousingContactItem) {
                    $contacts[] = $HousingContactItem;
                }
            }

            $clients = [];

            if ($item->Housing_client != null) {
                foreach ($item->Housing_client as $HousingClientItem) {
                    $clients[] = $HousingClientItem->Client;
                }
            }

            $item->contacts = $contacts;
            $item->clients = $clients;
            $item->title = $item->title . ' ' . $item->address;
        }

        return view('housing.view')
            ->with('housing', $item);
    }

    public function addIndex(Request $req)
    {
        $item = null;

        if ($req->has('id')) {
            $item = Housing::with('Housing_client')
                ->with('Housing_client.Client')
                ->with('Housing_contact')
                ->with('Gallery')
                ->find($req->id);

            if ($item->City != null) {
                $item->city_name = $item->City->name;
            }

            $gallery_files = [];

            if ($item->Gallery != null) {
                foreach ($item->Gallery as $GalleryItem) {
                    $gallery_files[] = [
                        'path' => $GalleryItem->path,
                        'name' => $GalleryItem->original_name
                    ];
                }
            }

            $item->gallery_files = $gallery_files;

            $contacts = [];

            if ($item->Housing_contact != null) {
                foreach ($item->Housing_contact as $HousingContactItem) {
                    $contacts[] = $HousingContactItem;
                }
            }

            $clients = [];

            if ($item->Housing_client != null) {
                foreach ($item->Housing_client as $HousingClientItem) {
                    $clients[] = $HousingClientItem->Client;
                }
            }

            $item->contacts = $contacts;
            $item->clients = $clients;
            $item->title = $item->title . ' ' . $item->address;
        }

        return view('housing.add')
            ->with('housing', $item);
    }

    public function create(Request $req)
    {
        $is_new = true;

        $niceNames = [
            'title' => '«Название»',
            'address' => '«Адрес»',
            'zip_code' => '«Индекс»',
            'city' => '«Город»',
            'clients' => '«Место работы»',
            'places_count' => '«Количество мест»',
            'cost' => '«Стоимость жилья»',
            'cost_per_day' => '«Стоимость за сутки»',
            'firstName.*' => '«Имя»',
            'lastName.*' => '«Фамилия»',
            'email.*' => '«Email»',
            'phone.*' => '«Телефон»',
        ];

        $validate_arr = [
            'title' => '',
            'address' => 'required',
            'zip_code' => 'required|numeric',
            'city' => '',
            'clients' => '',
            'places_count' => 'required|numeric',
            'cost' => 'required|numeric',
            'cost_per_day' => 'required|numeric',
            'firstName.*' => 'required',
            'lastName.*' => 'required',
            'email.*' => 'required|email:rfc,dns',
            'phone.*' => 'required|regex:/\+[0-9]{9,12}/',
        ];

        $validator = Validator::make($req->all(), $validate_arr, [], $niceNames);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        if ($fErr = $this->checkGalleryFiles($req)) {
            return response($fErr, 200);
        }

        if ($req->has('id')) {
            $is_new = false;
            $item = Housing::find($req->id);
            $item->active = $req->active;
        } else {
            $is_new = true;
            $item = new Housing;
        }

        $item->address = $req->address;
        $item->zip_code = $req->zip_code;
        $item->city = $req->city;
        $item->places_count = $req->places_count;
        $item->cost = $req->cost;
        $item->cost_per_day = $req->cost_per_day;

        $item->save();

        if ($is_new) {
            $item->title = $item->id;
        }

        if ($req->clients) {
            Housing_client::where('housing_id', $item->id)->delete();

            foreach ($req->clients as $clientId) {
                $new_client = new Housing_client;
                $new_client->housing_id = $item->id;
                $new_client->client_id = $clientId;
                $new_client->save();
            }
        }

        $item->save();

        $this->addContacts($req, $item->id);

        $this->addGalleryFiles($req, $item->id);

        return response(array('success' => "true"), 200);
    }

    private function addContacts($req, $h_id)
    {
        Housing_contact::where('housing_id', $h_id)->delete();

        if (!$req->firstName) {
            return null;
        }

        foreach ($req->firstName as $key => $firstName) {
            $cont = new Housing_contact;

            $cont->user_id = Auth::user()->id;
            $cont->housing_id = $h_id;
            $cont->firstName = $firstName;
            $cont->lastName = $req->lastName[$key];
            $cont->email = $req->email[$key];
            $cont->phone = $req->phone[$key];

            $cont->save();
        }
    }

    private static function checkGalleryFiles($r)
    {
        if (empty($r->file)) {
            return null;
        }

        foreach ($r->file as $fileItem) {
            if ($fileItem->isValid()) {
                $ext = $fileItem->getClientOriginalExtension();

                if ($ext != 'jpeg' && $ext != 'jpg' && $ext != 'png' && $ext != 'gif' && $ext != 'pdf') {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый тип файла'
                    );
                }

                if ($fileItem->getSize() > 5000000) {
                    return array(
                        'success' => "false",
                        'error' => 'Недопустимый вес файла'
                    );
                }
            } else {
                return array(
                    'success' => "false",
                    'error' => 'Файл повреждён и не может быть загружен!'
                );
            }
        }
    }

    private function addGalleryFiles($req, $h_id)
    {
        if ($req->to_delete_files) {
            foreach ($req->to_delete_files as $fName) {
                C_file::where('housing_id', $h_id)
                    ->where('original_name', $fName)
                    ->delete();
            }
        }

        if ($req->file) {
            foreach ($req->file as $fileItem) {
                $path = '/uploads/housing/' . Carbon::now()->format('m.Y') . '/' . $h_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file->housing_id = $h_id;
                $file->user_id = Auth::user()->id;
                $file->type = 1;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->save();
            }
        }
    }

    public function getRoomsJson(Request $req)
    {
        $draw = request()->get('draw');
        $start = request()->get("start");
        $rowperpage = request()->get("length");

        $filtered_count = $this->prepareGetRoomsJsonRequest($req);
        $filtered_count = $filtered_count->count();

        $items = $this->prepareGetRoomsJsonRequest($req);
        $items = $items->orderBy('number');

        $items = $items
            ->with('Candidates')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data = [];

        foreach ($items as $key => $item) {
            $data[$key] = $item;

            $data[$key]['filled_count'] = 0;

            if ($item->Candidates) {
                $data[$key]['filled_count'] = count($item->Candidates);
            }

            $data[$key]['free_count'] = $item->places_count - $data[$key]['filled_count'];
        }

        return response()->json([
            'data' => $data,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetRoomsJsonRequest($req)
    {
        return Housing_room::where('housing_id', $req->housing_id);
    }

    public function createRoom(Request $req)
    {
        $niceNames = [
            'number' => '«Номер комнаты»',
            'places_count' => '«Кол-во спальных мест»',
            'filled_count' => '«Заселено»',
        ];

        $validate_arr = [
            'number' => 'required',
            'places_count' => 'required|numeric',
            'filled_count' => 'nullable|numeric',
        ];

        $validator = Validator::make($req->all(), $validate_arr, [], $niceNames);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response(array('success' => "false", 'error' => $error), 200);
        }

        $exist_room_count = Housing_room::where('housing_id', $req->housing_id)
            ->where('number', $req->number)
            ->count();

        if ($req->has('id')) {
            $item = Housing_room::find($req->id);

            if ($item->number != $req->number && $exist_room_count > 0) {
                return response(array('success' => "false", 'error' => 'Комната с таким номером уже добавлена'), 200);
            }
        } else {
            if ($exist_room_count > 0) {
                return response(array('success' => "false", 'error' => 'Комната с таким номером уже добавлена'), 200);
            }

            $item = new Housing_room;
        }

        $item->housing_id = $req->housing_id;
        $item->number = $req->number;
        $item->places_count = $req->places_count;
        $item->filled_count = $req->filled_count;

        $item->save();

        return response(array('success' => "true"), 200);
    }

    public function removeRoom(Request $req)
    {
        $item = Housing_room::find($req->id);

        if ($item) {
            $item->delete();
            return response(array('success' => "true"), 200);
        } else {
            return response(array('success' => "false", 'error' => 'Комната не найдена'), 200);
        }
    }
}
