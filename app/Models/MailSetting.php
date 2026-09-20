<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    public $incrementing = false;

    protected $fillable = ['host', 'port', 'encryption', 'username', 'password', 'from_address', 'from_name'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['port' => 'integer', 'password' => 'encrypted'];
    }
}
