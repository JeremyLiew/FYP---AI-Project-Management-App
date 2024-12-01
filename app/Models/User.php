<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'application_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    ### Relationships
    public function applicationRole()
    {
        return $this->belongsTo(ApplicationRole::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'user_project_mappings');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'user_task_mappings');
    }

    public function generateVerificationUrl()
    {
        $token = sha1($this->email . $this->created_at); // Use a unique value for token generation
        return url(route('email.verify', ['user' => $this->id, 'token' => $token]));
    }

}
