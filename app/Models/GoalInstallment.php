<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalInstallment extends Model
{
    protected $fillable = [
        'user_id',
        'goal_id',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}
