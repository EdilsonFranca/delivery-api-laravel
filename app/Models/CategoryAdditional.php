<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAdditional extends Model
{
    use HasFactory;
    protected $table      ='category_additional';
    protected $primaryKey = 'id';

    public $timestamps   = false;
    protected $fillable  = array('id','category_id','additional_id');
}
