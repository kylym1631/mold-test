<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\C_file;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarsController extends Controller
{
    public function getIndex()
    {
        return view('cars.index');
    }

    public function getJson()
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
            ->with([
                'User',
                'Gallery' => function ($q) {
                    $q->where('type', 2);
                }
            ])
            ->skip($start)
            ->take($rowperpage)
            ->get();


        if ($items) {
            foreach ($items as $key => $item) {
                $doc_files = [];
                if ($item->Gallery != null) {
                    foreach ($item->Gallery as $GalleryItem) {
                        $doc_files[] = [
                            'path' => $GalleryItem->path,
                            'name' => $GalleryItem->original_name,
                            'ext' => $GalleryItem->ext,
                        ];
                    }
                }

                $items[$key]->doc_files = $doc_files;

                $user_name = '';

                if ($item->User) {
                    $user_name = mb_strtoupper($item->User->firstName .' '. $item->User->lastName);
                }

                $items[$key]->user_name = $user_name;
            }
        }

        return response()->json([
            'data' => $items,
            'draw' => $draw,
            'recordsTotal' => $filtered_count,
            'recordsFiltered' => $filtered_count,
        ], 200);
    }

    private function prepareGetJsonRequest($active, $users, $search)
    {
        $items = Car::query();

        if ($active != '') {
            $items = $items->where('active', $active);
        }

        if ($users != '') {
            $items = $items->whereIn('user_id', $users);
        }

        if (Auth::user()->isKoordinator()) {
            $items = $items->where('user_id', Auth::user()->id);
        }

        $items = $items->when($search, function ($query, $search) {
            return $query->where('brand', 'LIKE', '%' . $search . '%')
                ->orWhere('model', 'LIKE', '%' . $search . '%')
                ->orWhere('number', 'LIKE', '%' . $search . '%');
        });

        return $items;
    }

    public function itemView(Request $req)
    {
        $item = null;

        if ($req->has('id')) {
            $item = Car::where('id', $req->id);

            if (Auth::user()->isKoordinator()) {
                $item = $item->where('user_id', Auth::user()->id);
            }

            $item = $item->with('Gallery')
                ->with('User')
                ->first();

            if (!$item) {
                return abort(404);
            }

            $gallery_files = [];
            $doc_files = [];

            if ($item->Gallery != null) {
                foreach ($item->Gallery as $GalleryItem) {
                    if ($GalleryItem->type == 1) {
                        $gallery_files[] = [
                            'path' => $GalleryItem->path,
                            'name' => $GalleryItem->original_name,
                        ];
                    } else {
                        $doc_files[] = [
                            'path' => $GalleryItem->path,
                            'name' => $GalleryItem->original_name,
                            'ext' => $GalleryItem->ext,
                        ];
                    }
                }
            }

            $item->gallery_files = $gallery_files;
            $item->doc_files = $doc_files;
        }

        if ($req->is('*cars/add*')) {
            return view('cars.add')->with('item', $item);
        } else {
            return view('cars.view')->with('item', $item);
        }
    }

    public function create(Request $req)
    {
        $is_new = true;

        $niceNames = [
            'brand' => '«Марка»',
            'model' => '«Модель»',
            'number' => '«Номер»',
            'year' => '«Год»',
            'rent_cost' => '«Стоимость аренды»',
        ];

        $validate_arr = [
            'brand' => 'required',
            'model' => 'required',
            'number' => 'required',
            'year' => 'required|numeric',
            'rent_cost' => 'nullable|numeric|max:1000000',
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
            $item = Car::find($req->id);
            $item->active = $req->active;
        } else {
            $item = new Car;
        }

        $item->brand = $req->brand;
        $item->model = $req->model;
        $item->number = $req->number;
        $item->year = $req->year;
        $item->is_rent = $req->is_rent;
        $item->rent_cost = $req->rent_cost;
        $item->landlord = $req->landlord;
        $item->user_id = $req->user_id ?: null;

        $item->save();

        $this->addGalleryFiles($req, $item->id);

        return response(array('success' => "true"), 200);
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
                C_file::where('car_id', $h_id)
                    ->where('original_name', $fName)
                    ->delete();
            }
        }

        if ($req->file) {
            foreach ($req->file as $fileItem) {
                $path = '/uploads/cars/' . Carbon::now()->format('m.Y') . '/' . $h_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file->car_id = $h_id;
                $file->user_id = Auth::user()->id;
                $file->type = 1;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->save();
            }
        }

        if ($req->doc_file) {
            C_file::where('car_id', $h_id)
                ->where('type', 2)
                ->delete();

            foreach ($req->doc_file as $fileItem) {
                $path = '/uploads/cars/' . Carbon::now()->format('m.Y') . '/' . $h_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file->car_id = $h_id;
                $file->user_id = Auth::user()->id;
                $file->type = 2;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->save();
            }
        }
    }
}
