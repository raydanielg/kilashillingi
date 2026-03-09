<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'person_name',
        'amount',
        'description',
        'date',
        'due_date',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
