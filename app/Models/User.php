<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $permissions = null;

    public $roles = [
        '1' => 'Администратор',
        '2' => 'Рекрутер',
        '3' => 'Фрилансер',
        '4' => 'Логист',
        '5' => 'Трудоустройство',
        '6' => 'Координатор',
        '7' => 'Бухгалтер',
        '8' => 'Менеджер поддержки',
        '9' => 'Директор отдела рекрутации',
        '10' => 'Аудитор звонков',
        '11' => 'Маркетолог',
        '12' => 'Менеджер по легализации',
        '13' => 'Менеджер по недвижимости',
        '14' => 'Руководитель отдела трудоустройства',
    ];

    private function extractCustomRoles()
    {
        $items = Role::where('id', '>', 99)->get();

        foreach ($items as $m) {
            $this->roles[$m->id] = $m->name;
        }
    }

    public function getGroup()
    {
        if (!isset($this->roles[$this->group_id])) {
            $this->extractCustomRoles();
        }

        return $this->roles[$this->group_id];
    }

    public function getRoleTitle($id)
    {
        if (!isset($this->roles[$id])) {
            $this->extractCustomRoles();
        }

        return isset($this->roles[$id]) ? $this->roles[$id] : $id;
    }

    public function isAdmin()
    {
        if (Auth::user()->group_id == 1) {
            return true;
        } else {
            return false;
        }
    }
    public function isRecruiter()
    {
        if (Auth::user()->group_id == 2) {
            return true;
        } else {
            return false;
        }
    }
    public function isFreelancer()
    {
        if (Auth::user()->group_id == 3) {
            return true;
        } else {
            return false;
        }
    }
    public function isLogist()
    {
        if (Auth::user()->group_id == 4) {
            return true;
        } else {
            return false;
        }
    }
    public function isTrud()
    {
        if (Auth::user()->group_id == 5) {
            return true;
        } else {
            return false;
        }
    }
    public function isKoordinator()
    {
        if (Auth::user()->group_id == 6 || Auth::user()->group_id == 103) {
            return true;
        } else {
            return false;
        }
    }
    public function isCoordinator()
    {
        if (Auth::user()->group_id == 6 || Auth::user()->group_id == 103) {
            return true;
        } else {
            return false;
        }
    }
    public function isAccountant()
    {
        if (Auth::user()->group_id == 7) {
            return true;
        } else {
            return false;
        }
    }
    public function isSupportManager()
    {
        if (Auth::user()->group_id == 8) {
            return true;
        } else {
            return false;
        }
    }
    public function isRecruitmentDirector()
    {
        if (Auth::user()->group_id == 9) {
            return true;
        } else {
            return false;
        }
    }
    public function isCallAuditor()
    {
        if (Auth::user()->group_id == 10) {
            return true;
        } else {
            return false;
        }
    }
    public function isMarketer()
    {
        if (Auth::user()->group_id == 11) {
            return true;
        } else {
            return false;
        }
    }
    public function isLegalizationManager()
    {
        if (Auth::user()->group_id == 12) {
            return true;
        } else {
            return false;
        }
    }
    public function isRealEstateManager()
    {
        if (Auth::user()->group_id == 13) {
            return true;
        } else {
            return false;
        }
    }
    public function isHeadOfEmploymentDepartment()
    {
        if (Auth::user()->group_id == 14) {
            return true;
        } else {
            return false;
        }
    }

    public function getActivation()
    {
        if ($this->activation == 1) {
            return 'Активирован';
        } else if ($this->activation == 2) {
            return 'Деактивирован';
        }
    }

    public function getFl_status()
    {
        if ($this->fl_status == 1) {
            return 'Новый';
        } else if ($this->fl_status == 2) {
            return 'Верифицирован';
        } else if ($this->fl_status == 2) {
            return 'Отклонён';
        } else if ($this->fl_status == 2) {
            return 'Уволен';
        }
    }
    public function getPaymentFl()
    {
        if ($this->account_type == 1) {
            return 'Польский';
        } else if ($this->account_type == 2) {
            return 'Заграничный';
        } else if ($this->account_type == 3) {
            return 'PayPal';
        }
    }


    public function D_file()
    {
        return $this->hasOne(C_file::class)->where('type', 1);
    }

    public function Recruter()
    {
        return $this->belongsTo(User::class, 'recruter_id');
    }
    public function Manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function Candidates()
    {
        return $this->hasMany(Candidate::class, 'user_id')->where('removed', false);
    }

    public function RecruiterCandidates()
    {
        return $this->hasMany(Candidate::class, 'recruiter_id')->where('removed', false);
    }

    public function RecruitmentDirector()
    {
        return $this->belongsTo(User::class, 'user_id')->where('group_id', 9);
    }

    public function Tasks()
    {
        return $this->hasMany(Task::class, 'to_user_id');
    }

    public function LeadsSettings()
    {
        return $this->hasMany(UserOption::class)->where('key', 'leads_settings');
    }

    public function LeadsStatusesHistory()
    {
        return $this->hasMany(FieldsMutation::class)->where('model_name', 'Lead')->where('field_name', 'status');
    }

    public function FieldsMutations()
    {
        return $this->hasMany(FieldsMutation::class);
    }

    public function Cars()
    {
        return $this->hasMany(Car::class);
    }

    public function getPermissions()
    {
        if ($this->permissions === null) {
            $permissions = [];

            // $items = UserPermission::where('user_id', Auth::user()->id)->pluck('alias');
            // $permissions = $items->toArray();

            $static_role_permissions = RolePermission::getStaticPermissions(Auth::user()->group_id);
            $permissions = array_merge($permissions, $static_role_permissions);

            $role_permissions = RolePermission::where('role_id', Auth::user()->group_id)->pluck('alias');
            $permissions = array_merge($permissions, $role_permissions->toArray());

            $this->permissions = $permissions;
        }

        return $this->permissions;
    }

    public function hasPermission($aliases)
    {
        $permissions = $this->getPermissions();

        $res = false;

        if ($permissions) {
            if (gettype($aliases) == 'array') {
                foreach ($aliases as $alias) {
                    if (in_array($alias, $permissions)) {
                        $res = true;
                    }
                }
            } else {
                if (in_array($aliases, $permissions)) {
                    $res = true;
                }
            }
        }

        return $res;
    }

    public function getChildPermissions($root)
    {
        $permissions = $this->getPermissions();

        $res = [];

        if ($permissions) {
            foreach ($permissions as $perm) {
                $arr = explode($root, $perm);

                if (isset($arr[1])) {
                    $res[] = $arr[1];
                }
            }
        }

        return $res;
    }
}
