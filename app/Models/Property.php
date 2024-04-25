<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [

        'name', 'class', 'type', 'purchase_date', 'title_deed_no', 'mortgage_status', ' community', 'plot_no', 'bldg_no', 'bldg_name', 'property_no', 'floor_detail', 'suite_area', 'balcony_area', 'area_sq_mter', 'common_area', 'area_sq_feet',
        'owner_id', 'purchase_value', 'dewa_premise_no', 'dewa_ac_no', 'status', 'salesdeed'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
