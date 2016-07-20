<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

	/**
    * Relationship with Outcome registries
    */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
