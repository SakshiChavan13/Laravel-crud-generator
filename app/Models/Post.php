<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'user_id', 'owner_id', 'is_restricted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
