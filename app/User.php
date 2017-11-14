<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sent() {
        return $this->hasMany('App\Message', 'sender_id');
    }

    public function received() {
        return $this->belongsToMany('App\Message', 'message_user', 'recipient_id', 'message_id')->withPivot('is_starred', 'is_read')->withTimestamps();;
    }
}
