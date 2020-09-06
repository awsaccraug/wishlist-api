<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Wish extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'due_date', 'wisher_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public function wisher() {
        return $this->belongsTo('App\Wisher');
    }
}
