<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'order', 'board_id'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
