<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'columns'];

    protected function casts()
    {
        return [
            'columns' => 'array',
        ];
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}
