<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function sender() {
      return $this->belongsTo('App\User', 'sender_id');
    }

    public function recipients() {
      return $this->belongsToMany('App\User', 'message_user', 'message_id', 'recipient_id');
    }

}
