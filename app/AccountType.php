<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_type';

    protected $primaryKey = 'id_tipo_cuenta';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
