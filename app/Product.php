<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id', 'name', 'price', 'stock'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function category(){
        return $this->belongsTo('App\Category');
    }

    public function transaction(){
        return $this->belongsToMany('App\Transaction');
    }
}
