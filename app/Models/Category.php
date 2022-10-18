<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table      ='categories';
    protected $primaryKey = 'id';

    public $timestamps   = true;
    protected $fillable  = array('id','name');

    public function category_additional(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CategoryAdditional::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

}
