<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';
    public $timestamps = true;
    protected $primaryKey 	= 'id_order_product';

    protected $fillable = array('id_order_product', 'order_id', 'product_id', 'description' , 'quantity');

    public function product(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Product::class ,'id' ,'product_id');
    }

}
