<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['content', 'user_id', 'column_id', 'order_by', 'is_read'];

    protected $casts = [
        'is_read' => 'bool'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function votes()
    {
        return $this->belongsToMany(User::class, 'reply_votes')->withTimestamps();
    }
}
