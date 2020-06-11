<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'address', 'telephone'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function transaction(){
        return $this->hasMany('App\Transaction');
    }
}
