<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'status', 'priority', 'budget_id'];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_project_mappings')
        ->withPivot('project_role_id');
    }

    public function userProjectMappings()
    {
        return $this->hasMany(UserProjectMapping::class);
    }    

}
