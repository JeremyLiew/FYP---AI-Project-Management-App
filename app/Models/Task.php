<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'description', 'due_date', 'status', 'priority'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_task_mappings');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function userTaskMappings()
    {
        return $this->hasMany(UserTaskMapping::class);
    }

}
