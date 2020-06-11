<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTransaction extends Model
{
    protected $table = 'product_transaction';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transaction_id', 'product_id', 'product_name', 'product_price', 'quantity', 'total_price'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
