<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        's_no',
        'cell_id',
        'subject',
        'letter_no',
        'letter_date',
        'received_from',
        'name_of_da',
        'received_date'
    ];

    protected $casts = [
        'letter_date' => 'date',
    ];

    public function cell(): BelongsTo
    {
        return $this->belongsTo(Cell::class);
    }
}
