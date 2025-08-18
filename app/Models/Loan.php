<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'borrower_id',
        'start_date',
        'due_date',
        'returned_at',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class);
    }

    public function borrower()
    {
        return $this->belongsTo(\App\Models\User::class, 'borrower_id');
    }

    public function review()
    {
        return $this->hasOne(\App\Models\Review::class);
    }
}
