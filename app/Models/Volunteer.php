<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'project_id',
        'status',
        'message',
    ];
    
    /**
     * Get the user that volunteered
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the project that the user volunteered for
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}