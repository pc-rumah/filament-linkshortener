<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkClicks extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function link()
    {
        return $this->belongsTo(Links::class);
    }
}
