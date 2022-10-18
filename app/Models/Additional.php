<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Additional extends Model
{
    use HasFactory;
    protected $table ='additional';
    public $timestamps = true;
    protected $fillable  = array('id','name','price');
}

