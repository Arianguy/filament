<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'paytype', 'cheqno', 'cheqbank', 'cheqamt', 'cheqdate', 'trans_type', 'narration', 'depositdate', 'cheqstatus', 'depositac', 'remarks', 'cheq_img'
    ];

    public function Contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
