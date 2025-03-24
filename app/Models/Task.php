<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'due_date'
    ];

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
