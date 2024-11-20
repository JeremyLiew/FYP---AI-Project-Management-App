<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "final_price",
        "order_quantity",
        "order_status",
        'token',
        'user_id'
    ];

    ### Relationships
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_order_mappings', 'order_id', 'product_id')->withPivot('quantity', 'size');
        ;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    protected $hidden = [
       'token',
    ];
}
