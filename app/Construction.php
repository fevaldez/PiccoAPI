<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Construction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'constructions';

    protected $primaryKey = 'id_proyecto';

	/**
    * Relationship with income registries
    */
	public function policy()
    {
        return $this->hasMany('App\Policy', 'id_proyecto');
    }

	/**
    * Relationship with income registries
    */
    public function policiesDet()
    {
        return $this->hasMany('App\PoliciesDet', 'id_proyecto');
    }
}
