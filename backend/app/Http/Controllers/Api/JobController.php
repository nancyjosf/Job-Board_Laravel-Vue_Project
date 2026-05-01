<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'location' => ['nullable', 'string', 'max:120'],
            'work_type' => ['nullable', 'in:remote,onsite,hybrid'],
            'experience_level' => ['nullable', 'in:junior,mid,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0', 'lte:salary_max'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'date_posted' => ['nullable', 'in:24h,7d,30d'],
            'sort' => ['nullable', 'in:newest,oldest,salary_desc,salary_asc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = JobListing::query()
            ->public()
            ->with('category');

        $query = $this->applySearchAndFilters($query, $validated);

        $this->applySorting($query, $validated['sort'] ?? null);

        $perPage = (int) ($validated['per_page'] ?? 12);
        $jobs = $query->paginate($perPage)->withQueryString();

        return JobListingResource::collection($jobs);
    }

   
    private function applySearchAndFilters(Builder $query, array $validated): Builder
    {
        if (!empty($validated['search'])) {
            $search = trim((string) $validated['search']);
            $query->where(function (Builder $sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', (int) $validated['category_id']);
        }

        if (!empty($validated['location'])) {
            $loc = trim((string) $validated['location']);
            $query->where('location', 'like', "%{$loc}%");
        }

        if (!empty($validated['work_type'])) {
            $query->where('work_type', (string) $validated['work_type']);
        }

        if (!empty($validated['experience_level'])) {
            $query->where('experience_level', (string) $validated['experience_level']);
        }

        if (isset($validated['salary_min'])) {
            $query->where('salary_min', '>=', (int) $validated['salary_min']);
        }

        if (isset($validated['salary_max'])) {
            $query->where('salary_max', '<=', (int) $validated['salary_max']);
        }

        if (!empty($validated['date_posted'])) {
            $days = match ($validated['date_posted']) {
                '24h' => 1,
                '7d' => 7,
                '30d' => 30,
            };

            $query->where('published_at', '>=', now()->subDays($days));
        }

        return $query;
    }

    private function applySorting(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'salary_desc' => $query->orderByRaw('salary_max is null, salary_max desc'),
            'salary_asc' => $query->orderByRaw('salary_min is null, salary_min asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    public function show(JobListing $jobListing): JobListingResource
    {
        abort_unless(
            $jobListing->status === 'published'
                && $jobListing->published_at !== null
                && $jobListing->approved_at !== null,
            404
        );

        $jobListing->increment('views_count');
        $jobListing->loadMissing('category');
        return new JobListingResource($jobListing);
    }
}
