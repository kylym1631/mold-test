<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPositionRate extends Model
{
    use HasFactory;

    protected static $types = [
        'rate' => 'Ставка',
        'rate_after' => 'Ставка после 3 месяца',
        'personal_rate' => 'Ставка от клиента, brutto',
    ];

    public static function getTypeTitle($key)
    {
        return self::$types[$key];
    }
}
