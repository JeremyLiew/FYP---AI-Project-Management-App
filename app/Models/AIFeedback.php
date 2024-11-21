<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIFeedback extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ai_model', 'feedback', 'rating', 'feedbackable_id', 'feedbackable_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }
}
