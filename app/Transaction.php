<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id', 'invoice_number', 'amount', 'pay', 'change'
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function product(){
        return $this->belongsToMany('App\Product')->withPivot('quantity');
    }       
}
