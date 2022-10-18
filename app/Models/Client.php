<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model{
    protected $table ='client';
    public $timestamps=true;
    protected $fillable = array('name', 'phone', 'address', 'order_id');

    public function order() :\Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Order::class);
    }

}
