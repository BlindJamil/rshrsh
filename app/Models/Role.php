<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_role_user', 'admin_role_id', 'admin_user_id');
    }
} 