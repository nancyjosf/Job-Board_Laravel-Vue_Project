<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;

class JobListing extends Model
{
    /** @use HasFactory<\Database\Factories\JobListingFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'company_name',
        'company_logo',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'location',
        'work_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'published_at',
        'approved_at',
        'views_count',
        'applications_count',
         'user_id',
         'status',
         'deadline',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Scope public listings (published + approved).
     */
    public function scopePublic($query)
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereNotNull('approved_at');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}
