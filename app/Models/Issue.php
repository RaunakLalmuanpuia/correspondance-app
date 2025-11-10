<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Issue extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        's_no',
        'cell_id',
        'letter_addressee_main',
        'letter_addressee_copy_to',
        'subject',
        'letter_no',
        'letter_date',
        'issue_date',
    ];

    protected $casts = [
        'letter_addressee_copy_to' => 'array',
    ];
    public function cell(): BelongsTo
    {
        return $this->belongsTo(Cell::class);
    }

}
