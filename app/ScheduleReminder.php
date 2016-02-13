<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ScheduleReminder extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'schedule_reminder';

    public $timestamps = true;

    public function newQuery()
    {
        if(Auth::check()){
            $query = parent::newQuery();
            $query->whereHas('Schedule', function($q)
            {
                $q->where('user_id', Auth::user()->id);

            });
            return $query;
        }else{
            $query = parent::newQuery();
            $query->whereHas('Schedule', function($q)
            {
                $q->where('user_id', '!=', 0);

            });
            return $query;
        }
    }

    /* Start Boot */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->created_by = Auth::user()->id;
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function ($post) {
            $post->updated_by = Auth::user()->id;
        });
    }/* END Boot */

    public function Schedule()
    {
        return $this->belongsTo('App\Schedule', 'schedule_id', 'id');
    }
}
