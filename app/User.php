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
        return $this->hasMany('App\Message', 'sender_id')->where('sent_at', '!=', null);
    }

    public function drafts() {
        return $this->hasMany('App\Message', 'sender_id')->where('sent_at', '=', null);
    }

    public function received() {
        return 
            $this
                ->belongsToMany('App\Message', 'message_user', 'recipient_id', 'message_id')
                ->withPivot('is_starred', 'is_read', 'deleted_at')
                ->withTimestamps()
                ->where('sent_at', '!=', null)
                ->where('message_user.deleted_at', '=', null);
    }

    public function starred() {
        return 
            $this
                ->belongsToMany('App\Message', 'message_user', 'recipient_id', 'message_id')
                ->withPivot('is_starred', 'is_read', 'deleted_at')
                ->withTimestamps()
                ->where('sent_at', '!=', null)
                ->where('is_starred', true)
                ->where('message_user.deleted_at', '=', null);
    }

    public function trash() {

        // The trash can only shows deleted messages that others have sent to us!

        // TODO: Also show messages that we wrote and then deleted (sent or unsent)

        return 
            $this
                ->belongsToMany('App\Message', 'message_user', 'recipient_id', 'message_id')
                ->withPivot('is_starred', 'is_read', 'deleted_at')
                ->withTimestamps()
                ->where('sent_at', '!=', null)
                ->where('message_user.deleted_at', '!=', null);
    }

}
