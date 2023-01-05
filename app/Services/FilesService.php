<?php

namespace App\Services;

use App\Models\C_file;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FilesService
{
    public function checkFiles($r)
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

    public function addFiles($req, $model_field, $model_id)
    {
        if ($req->to_delete_files) {
            foreach ($req->to_delete_files as $fName) {
                C_file::where($model_field, $model_id)
                    ->where('original_name', $fName)
                    ->delete();
            }
        }

        if ($req->file) {
            foreach ($req->file as $fileItem) {
                $path = '/uploads/' . str_replace('_id', '', $model_field) . '/' . Carbon::now()->format('m.Y') . '/' . $model_id . '/files/';
                $name = Str::random(12) . '.' . $fileItem->getClientOriginalExtension();

                $fileItem->move(public_path($path), $name);
                $file_link = $path . $name;

                $file = new C_file();
                $file->autor_id = Auth::user()->id;
                $file[$model_field] = $model_id;
                $file->user_id = Auth::user()->id;
                $file->type = 1;
                $file->original_name = $fileItem->getClientOriginalName();
                $file->ext = $fileItem->getClientOriginalExtension();
                $file->path = $file_link;
                $file->save();
            }
        }
    }
}
