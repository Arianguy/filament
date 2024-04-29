<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'fname', 'eid', 'eidexp', 'nationality', 'email', 'mobile', 'visa', 'passportno', 'passexp', 'eidfront', 'eidback', 'frontpass', 'backpass', 'visa_img'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
