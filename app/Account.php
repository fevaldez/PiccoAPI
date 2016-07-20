<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    protected $primaryKey = 'account_id';
    
	/**
    * Relationship with income registries
    */
    public function policiesDet()
    {
        return $this->hasMany('App\PoliciesDet');
    }
}
