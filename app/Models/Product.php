<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model{
    protected $table ='products';
    public $timestamps=true;
    protected $fillable = array('id', 'name', 'price', 'price_promotion', 'description', 'category_id', 'photo');

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getCapaUrlAtribute(): string
    {
        return Storage::url($this->photo);
    }
}
