<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoliciesDet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'policies_det';

    protected $primaryKey = 'id_poliza_det';

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
    public function account()
    {
        return $this->belongsTo('App\Account');
    }

	/**
    * Relationship with income registries
    */
    public function policy()
    {
        return $this->belongsTo('App\Policy', 'id_poliza');
    }
}
