<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Message extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected static function boot() {
        parent::boot();

        // static::addGlobalScope('sent', function (Builder $builder) {
        //     $builder->where('sent_at', '!=', null);
        // });
    }

    public function sender() {
      return $this->belongsTo('App\User', 'sender_id');
    }

    public function recipients() {
      return $this->belongsToMany('App\User', 'message_user', 'message_id', 'recipient_id')->withPivot('is_starred', 'is_read')->withTimestamps();
    }

    public function prettySent() {
      $sent = Carbon::parse($this->sent_at);
      $time = '';
      if ($sent->hour > 11) {
        $time = $sent->hour - 12 . ':' . sprintf('%02d', $sent->minute) . ' pm';
      }
      else {
        $time = $sent->hour . ':' . sprintf('%02d', $sent->minute) . ' am';
      }
      $pretty = $sent->month . '/' . $sent->day . '/' . $sent->year . ' ' . $time;
      return $pretty;
    }

}
