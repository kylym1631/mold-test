<?php

namespace App\Services;

use App\Models\Option;

class OptionsService
{
    public function getByKeys($keys = [])
    {
        $options = Option::whereIn('key', $keys)->get();

        $result = [];

        if (!$options->isEmpty()) {
            foreach ($options as $opt) {
                $result[$opt->key] = $opt->value;
            }
        } else {
            foreach ($keys as $key) {
                $result[$key] = '';
            }
        }

        return $result;
    }
}