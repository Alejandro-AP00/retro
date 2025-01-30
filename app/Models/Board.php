<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'board_template_id',
        'owner_id',
        'locked_at',
        'team_id',
        'is_private'
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'is_private' => 'boolean',
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

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function isLocked()
    {
        return !is_null($this->locked_at) && $this->locked_at->isPast();
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function hasAccess(User $user)
    {
        if (!$this->is_private) {
            return $user->belongsToTeam($this->team);
        }

        return $user->belongsToTeam($this->team) && ($this->users()->where('user_id', $user->id)->exists() ||
               $user->id === $this->owner_id ||
               $user->hasPermissionTo('manage boards'));
    }
}
