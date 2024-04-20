<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'eid', 'eidexp', 'nationality', 'email', 'mobile', 'nakheelno', 'is_visible'
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
