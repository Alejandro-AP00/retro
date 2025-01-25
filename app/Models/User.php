<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the boards owned by the user.
     */
    public function ownedBoards()
    {
        return $this->hasMany(Board::class, 'owner_id');
    }

    /**
     * Get the replies created by the user.
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Get the replies that the user has voted on.
     */
    public function votedReplies()
    {
        return $this->belongsToMany(Reply::class, 'reply_votes')
            ->withTimestamps();
    }
}
