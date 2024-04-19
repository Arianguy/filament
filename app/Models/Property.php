<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [

        'name', 'class', 'type', 'purchasedate', 'titledeedno', 'mortgage', ' community', 'plotno', 'bldgno', 'bldgname', 'propertyno', 'floordetail', 'suitearea', 'balconyarea', 'areasqmter', 'commonarea', 'areasqfeet',
        'ownerid', 'purchasevalue', 'dewapremiseno', 'dewaacno', 'status', 'salesdeed'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }
}
