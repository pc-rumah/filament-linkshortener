<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Links extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
    ];

    public function clicks()
    {
        return $this->hasMany(LinkClicks::class, 'link_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        if (auth()->check()) {
            static::addGlobalScope('user', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }
    }
}
