<?php

namespace App\Models;

use Pylon\Traits\Models\HasModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pylon\Traits\Models\HasSoftDeletesActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
	use HasFactory;

	use InteractsWithMedia;

	use SoftDeletes;

	// use HasSoftDeletesActivity;

	// use HasModelTrait;

	protected $fillable = [
		'title',
		'description',
		'price'
	];

	protected $hidden = [];

	protected $casts = [];

	protected $appends = [];

	### Accessors

	### Mutators

	### Relationships
	public function orders()
	{
		return $this->belongsToMany(Order::class, 'product_order_mappings', 'product_id', 'order_id');
	}

	public function sizes()
	{
		return $this->hasMany(ProductSize::class)->where('quantity', '>', 0);
	}


	### Extend relationships

	### Methods

	### Static Methods
}
