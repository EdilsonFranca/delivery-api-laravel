<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table ='order';
    public $timestamps=true;
    protected $primaryKey 	= 'id_order';
    protected $fillable = array('id_order', 'total', 'subTotal', 'deliveryFee', 'formOfPayment','thing', 'client_id');


    public function order_product(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id_order');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Client::class ,'id_client' ,'client_id');
    }
}
