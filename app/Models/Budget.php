<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'total_budget', 'remaining_amount'];

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
