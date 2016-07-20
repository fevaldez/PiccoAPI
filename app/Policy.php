<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'policies';

    protected $primaryKey = 'id_poliza';
    

	/**
    * Relationship with income registries
    */
    public function construction()
    {
        return $this->belongsTo('App\Construction', 'id_proyecto');
    }

	/**
    * Relationship with income registries
    */
    public function policiesDet()
    {
        return $this->hasMany('App\PoliciesDet', 'id_poliza');
    }
}
