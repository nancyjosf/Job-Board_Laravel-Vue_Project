<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    
    public function jobListings()
    {
        return $this->hasMany(JobListing::class);
    }


protected static function booted()
{
    static::creating(function ($category) {
        $category->slug = Str::slug($category->name);
    });
}
}