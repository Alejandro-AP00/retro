<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'board_template_id'];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function template()
    {
        return $this->belongsTo(BoardTemplate::class, 'board_template_id');
    }

    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    public function isLocked()
    {
        return !is_null($this->locked_at) && $this->locked_at->isPast();
    }
}
