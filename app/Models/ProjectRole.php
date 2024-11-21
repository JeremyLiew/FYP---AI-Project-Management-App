<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function userProjectMappings()
    {
        return $this->hasMany(UserProjectMapping::class, 'project_role_id');
    }
}
