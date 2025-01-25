<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'column_id'];

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
        return $this->hasMany(ReplyVote::class);
    }

    public function voters()
    {
        return $this->belongsToMany(User::class, 'reply_votes');
    }
}
