<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardTemplate extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['name', 'description', 'columns', 'team_id'];

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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
