<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = 'proyectos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
    * Relationship with income registries
    */
    public function income()
    {
        return $this->hasMany('App\Income');
    }

    /**
    * Relationship with Outcome registries
    */
    public function outcome()
    {
        return $this->hasMany('App\Outcome');
    }
}   