<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\JobListing;
class Comment extends Model
{
protected $fillable=[
    'user_id',
    'job_listing_id',
    'content',
];
public function user()
{
    return $this->belongsTo(User::class);
}
public function jobListing()
{
    return $this->belongsTo(JobListing::class);
}
}
