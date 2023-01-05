<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RolePermission extends Model
{
    use HasFactory;

    private static $permissions = [
        [
            'name' => 'Кандидаты',
            'key' => 'candidate',
            'children' => [
                [
                    'key' => 'view',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'delete',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Работники (Трудоустроенные кандидаты)',
            'key' => 'employee',
            'children' => [
                [
                    'key' => 'view',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'edit',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'delete',
                    'select' => [
                        'name' => 'Статус',
                        'key' => 'status',
                        'options' => [],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Сотрудники',
            'key' => 'user',
            'children' => [
                [
                    'key' => 'view',
                    'select' => [
                        'name' => 'Роль',
                        'key' => 'role',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'viewAnothers',
                    'name' => 'Просмотр чужих сотрудников',
                ],
                [
                    'key' => 'create',
                    'select' => [
                        'name' => 'Роль',
                        'key' => 'role',
                        'options' => [],
                    ],
                ],
                [
                    'key' => 'edit',
                    'select' => [
                        'name' => 'Роль',
                        'key' => 'role',
                        'options' => [],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Вакансии',
            'key' => 'vacancy',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Фрилансеры',
            'key' => 'freelancer',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Клиенты',
            'key' => 'client',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Лиды',
            'key' => 'lead',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'setting',
                    'name' => 'Настройка пакетов',
                ],
                [
                    'key' => 'import',
                    'name' => 'Импорт из Excel',
                ],
            ],
        ],
        [
            'name' => 'Статистика',
            'key' => 'statistics',
            'children' => [
                [
                    'key' => 'view',
                ],
            ],
        ],
        [
            'name' => 'Жилье',
            'key' => 'housing',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Машины',
            'key' => 'cars',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Настройки',
            'key' => 'handbook',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
                [
                    'key' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'Фирмы',
            'key' => 'firm',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
                [
                    'key' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'Шаблоны документов',
            'key' => 'templates',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Регулярные перевозки',
            'key' => 'transportations',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
        [
            'name' => 'Задачи (все задачи)',
            'key' => 'task',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
            ],
        ],
        [
            'name' => 'Шаблоны задач',
            'key' => 'taskTemplate',
            'children' => [
                [
                    'key' => 'view',
                ],
                [
                    'key' => 'create',
                ],
                [
                    'key' => 'edit',
                ],
            ],
        ],
    ];

    private static $roles = null;

    public static function get()
    {
        if (self::$roles == null) {
            self::$roles = Role::where('id', '>', 1)->get();
        }

        $statuses = Candidate::getStatuses();

        $permissions = self::$permissions;

        foreach ($permissions as $key => $item) {
            self::$permissions[$key]['comp_key'] = $item['key'];

            foreach ($item['children'] as $c_key => $c_item) {
                if ($c_item['key'] == 'view') {
                    self::$permissions[$key]['children'][$c_key]['name'] = 'Просмотр';
                } else
                if ($c_item['key'] == 'create') {
                    self::$permissions[$key]['children'][$c_key]['name'] = 'Создание';
                } else
                if ($c_item['key'] == 'edit') {
                    self::$permissions[$key]['children'][$c_key]['name'] = 'Редактирование';
                } else
                if ($c_item['key'] == 'delete') {
                    self::$permissions[$key]['children'][$c_key]['name'] = 'Удаление';
                }

                self::$permissions[$key]['children'][$c_key]['comp_key'] = $item['key'] . '.' . $c_item['key'];

                if (isset($c_item['select'])) {

                    self::$permissions[$key]['children'][$c_key]['select']['comp_key'] = $item['key'] . '.' . $c_item['key'] . '.' . $c_item['select']['key'];

                    if ($c_item['select']['key'] == 'status') {
                        foreach ($statuses as $st_id => $st_name) {
                            if (($item['key'] == 'candidate' && !in_array($st_id, [7, 8, 9]))
                                || ($item['key'] == 'employee' && in_array($st_id, [7, 8, 9]))
                            ) {
                                self::$permissions[$key]['children'][$c_key]['select']['options'][] = [
                                    'name' => $st_name,
                                    'key' => $st_id,
                                    'comp_key' => $item['key'] . '.' . $c_item['key'] . '.' . $c_item['select']['key'] . '.' . $st_id,
                                ];
                            }
                        }
                    } else
                        if ($c_item['select']['key'] == 'role') {
                        foreach (self::$roles as $role) {
                            self::$permissions[$key]['children'][$c_key]['select']['options'][] = [
                                'name' => $role->name,
                                'key' => $role->id,
                                'comp_key' => $item['key'] . '.' . $c_item['key'] . '.' . $c_item['select']['key'] . '.' . $role->id,
                            ];
                        }
                    }
                }
            }
        }

        return self::$permissions;
    }

    public static function getAll()
    {
        $res = self::get();
        return $res;
    }

    public static function getName($alias)
    {
        return isset(self::get()[$alias]) ? self::get()[$alias] : $alias;
    }

    public static function getStaticPermissions($group_id)
    {
        $perm = [
            '1' => [ // Admin
                'candidate.view',
                'candidate.edit',
                'candidate.delete',
                'employee.view',
                'employee.edit',
                'employee.delete',
                'user.view',
                'user.create',
                'user.edit',
                'vacancy.view',
                'vacancy.create',
                'vacancy.edit',
                'freelancer.view',
                'freelancer.create',
                'freelancer.edit',
                'client.view',
                'client.create',
                'client.edit',
                'lead.view',
                'lead.setting',
                'lead.import',
                'statistics.view',
                'housing.view',
                'housing.create',
                'housing.edit',
                'cars.view',
                'cars.create',
                'cars.edit',
                'handbook.view',
                'handbook.create',
                'handbook.edit',
                'handbook.delete',
                'firm.view',
                'firm.create',
                'firm.edit',
                'firm.delete',
                'templates.view',
                'templates.create',
                'templates.edit',
                'task.view',
                'task.create',
                'taskTemplate.view',
                'taskTemplate.create',
                'taskTemplate.edit',
            ],
            '2' => [ // Recruiter
                'candidate.view',
                'candidate.create',
                'candidate.edit',
                // 'freelancer.view',
                'vacancy.view',
            ],
            '3' => [ // Freelancer
                'candidate.view',
                'candidate.create',
                'vacancy.view',
            ],
            '4' => [ // Logist
                'candidate.view',
                'candidate.edit',
            ],
            '5' => [ // Trud
                'candidate.view',
                'candidate.edit',
                'employee.view',
                'employee.edit',
            ],
            '6' => [ // Coordinator
                // 'candidate.view',
                // 'candidate.edit',
                // 'employee.view',
                // 'employee.edit',
                // 'client.view',
                // 'housing.view',
                // 'housing.create',
                // 'housing.edit',
                // 'housing.delete',
                // 'cars.view',
                // 'cars.create',
                // 'cars.edit',
            ],
            '7' => [ // Accountant
                'client.view',
                'firm.view',
                'firm.create',
                'firm.edit',
                'firm.delete',
            ],
            '8' => [ // SupportManager
                'freelancer.view',
            ],
            '9' => [ // RecruitmentDirector
                'candidate.view',
                'candidate.edit',
                'employee.view',
                'employee.edit',
                'vacancy.view',
                'lead.view',
                'lead.setting',
                'lead.import',
                'statistics.view',
                'housing.view',
                'housing.create',
                'housing.edit',
            ],
            '11' => [ // Marketer
                'lead.view',
                'lead.setting',
                'lead.import',
                'statistics.view',
            ],
            '12' => [ // LegalizationManager
                'candidate.view',
                'candidate.edit',
                'employee.view',
                'employee.edit',
                'templates.view',
                'templates.create',
                'templates.edit',
            ],
            '13' => [ // RealEstateManager
                'client.view',
                'client.create',
                'housing.view',
                'housing.create',
                'housing.edit',
            ],
            '14' => [ // HeadOfEmploymentDepartment
                'vacancy.view',
            ],
        ];

        if ($group_id == '2') { // Recruiter
            foreach ([1, 2, 3, 5, 14] as $st) {
                $perm['2'][] = 'candidate.view.status.' . $st;
                $perm['2'][] = 'candidate.edit.status.' . $st;
            }

            // FOR Recruiter 113
            if (Auth::user()->id == 113) {
                $perm['2'][] = 'employee.view';
                $perm['2'][] = 'employee.edit';

                foreach ([10, 11] as $st) {
                    $perm['2'][] = 'candidate.view.status.' . $st;
                    $perm['2'][] = 'candidate.edit.status.' . $st;
                }

                foreach ([8, 7, 9] as $st) {
                    $perm['2'][] = 'employee.view.status.' . $st;
                    $perm['2'][] = 'employee.edit.status.' . $st;
                }
            }

        } else
        if ($group_id == '3') { // Freelancer
            foreach ([1, 2, 3, 4, 5, 10] as $st) {
                $perm['3'][] = 'candidate.view.status.' . $st;
                $perm['3'][] = 'candidate.edit.status.' . $st;
            }
        } else
        if ($group_id == '4') { // Logist
            foreach ([4, 6, 19, 20, 21] as $st) {
                $perm['4'][] = 'candidate.view.status.' . $st;
                $perm['4'][] = 'candidate.edit.status.' . $st;
            }
        } else
        if ($group_id == '5') { // Trud
            foreach ([19, 12, 22] as $st) {
                $perm['5'][] = 'candidate.view.status.' . $st;
                $perm['5'][] = 'candidate.edit.status.' . $st;
            }

            $perm['5'][] = 'employee.view.status.7';
            $perm['5'][] = 'employee.edit.status.7';
        } else
        if ($group_id == '6') { // Coordinator
            // foreach ([10, 11] as $st) {
            //     $perm['6'][] = 'candidate.view.status.' . $st;
            //     $perm['6'][] = 'candidate.edit.status.' . $st;
            // }

            // foreach ([8, 7, 9] as $st) {
            //     $perm['6'][] = 'employee.view.status.' . $st;
            //     $perm['6'][] = 'employee.edit.status.' . $st;
            // }
        } else
        if ($group_id == '7') { // Accountant
            
        } else
        if ($group_id == '9') { // RecruitmentDirector
            $statuses = Candidate::getStatuses();

            foreach ($statuses as $st => $st_name) {
                if (in_array($st, [7, 8, 9])) {
                    $perm['9'][] = 'employee.view.status.' . $st;
                    $perm['9'][] = 'employee.edit.status.' . $st;
                } else {
                    $perm['9'][] = 'candidate.view.status.' . $st;
                    $perm['9'][] = 'candidate.edit.status.' . $st;
                }
            }
        } else
        if ($group_id == '12') { // LegalizationManager
            $statuses = Candidate::getStatuses();

            foreach ($statuses as $st => $st_name) {
                if (in_array($st, [7, 8, 9])) {
                    $perm['12'][] = 'employee.view.status.' . $st;
                    $perm['12'][] = 'employee.edit.status.' . $st;
                } else {
                    $perm['12'][] = 'candidate.view.status.' . $st;
                    $perm['12'][] = 'candidate.edit.status.' . $st;
                }
            }
        }

        return isset($perm[$group_id]) ? $perm[$group_id] : [];
    }
}
