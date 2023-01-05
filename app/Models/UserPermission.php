<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    private static $items = [
        'user',
        'vacancy',
        'vacancy.create',
        'vacancy.edit',
        'freelancer',
        'client',
        'candidate',
        'lead',
        // 'arrival',
        'statistics',
        'housing',
        'cars',
        'handbook',
        'firm',
        'firm.create',
        'firm.edit',
        'firm.delete',
    ];

    public static function getName($alias)
    {
        $arr = [
            'user' => 'Сотрудники',
            'vacancy' => 'Вакансии',
            'vacancy.create' => 'Создание вакансии',
            'vacancy.edit' => 'Редактирование вакансии',
            'freelancer' => 'Фрилансеры',
            'client' => 'Клиенты',
            'candidate' => 'Кандидаты',
            'lead' => 'Лиды',
            'arrival' => 'Приезды',
            'statistics' => 'Статистика',
            'housing' => 'Жилье',
            'cars' => 'Машины',
            'handbook' => 'Настройки',
            'firm' => 'Фирмы',
            'firm.create' => 'Создание фирмы',
            'firm.edit' => 'Редактирование фирмы',
            'firm.delete' => 'Удаление фирмы',
        ];

        return isset($arr[$alias]) ? $arr[$alias] : $alias;
    }

    public static function getAll()
    {
        $res = [];

        foreach (self::$items as $item) {
            $res[$item] = self::getName($item);
        }

        return $res;
    }

    public static function getAllowedToRole($group_id)
    {
        $arr = [];

        if ($group_id == 1) {
            $arr = [];
        }

        if ($group_id == 2) {
            $arr = [
                'user',
                'client',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 3) {
            $arr = [
                'user',
                'freelancer',
                'client',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 4) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'lead',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 5) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'lead',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 6) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'lead',
                'arrival',
                'statistics',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 7) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
            ];
        }

        if ($group_id == 8) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'client',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 9) {
            $arr = [
                'freelancer',
                'client',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 10) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 11) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'candidate',
                'arrival',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 12) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'client',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 13) {
            $arr = [
                'user',
                'vacancy',
                'vacancy.create',
                'vacancy.edit',
                'freelancer',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        if ($group_id == 14) {
            $arr = [
                'user',
                'freelancer',
                'client',
                'candidate',
                'lead',
                'arrival',
                'statistics',
                'housing',
                'cars',
                'handbook',
                'firm',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ];
        }

        $res = [];

        foreach ($arr as $item) {
            if (in_array($item, self::$items)) {
                if (stripos($item, '.') > 0) {
                    $root = explode('.', $item);
                    $res[$root[0]]['children'][$item] = ['key' => $item, 'name' => self::getName($item)];
                } else {
                    $res[$item] = ['key' => $item, 'name' => self::getName($item)];
                }
            }
        }

        return $res;
    }
}
