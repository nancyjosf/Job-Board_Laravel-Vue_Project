<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class JobController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:200'], 
            'category_id' => ['nullable', 'integer'],
            'location' => ['nullable', 'string', 'max:120'],
            'work_type' => ['nullable', 'in:remote,onsite,hybrid'],
            'experience_level' => ['nullable', 'in:junior,mid,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'in:newest,oldest,salary_desc,salary_asc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = JobListing::query()->with('category'); 
        
        $query->where('status', 'published')->whereNotNull('published_at');
        
        $query = $this->applySearchAndFilters($query, $validated);
        $this->applySorting($query, $validated['sort'] ?? null);

        $perPage = (int) ($validated['per_page'] ?? 12);
        return JobListingResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string'],
            'work_type' => ['required', 'in:remote,onsite,hybrid'],
            'experience_level' => ['nullable', 'in:junior,mid,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ]);

        $job = JobListing::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Job created successfully as draft',
            'job' => new JobListingResource($job->load('category')),
        ], 201);
    }

    public function myJobs(Request $request)
    {
        $jobs = JobListing::where('user_id', $request->user()->id)
            ->with('category')
            ->latest()
            ->get();

        return JobListingResource::collection($jobs);
    }

    public function update(Request $request, JobListing $job)
    {
        if ($request->user()->id !== $job->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'status' => ['sometimes', 'in:draft,published,archived'],
        ]);

        $job->update($validated);

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => new JobListingResource($job->load('category'))
        ]);
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $stats = JobListing::where('user_id', $userId)
            ->selectRaw('count(*) as total_jobs')
            ->selectRaw("count(case when status = 'published' then 1 end) as published_jobs")
            ->selectRaw("count(case when status = 'draft' then 1 end) as draft_jobs")
            ->selectRaw("count(case when status = 'archived' then 1 end) as archived_jobs")
            ->selectRaw('sum(views_count) as total_views')
            ->first();

        return response()->json([
            'total_jobs' => (int) $stats->total_jobs,
            'published_jobs' => (int) $stats->published_jobs,
            'draft_jobs' => (int) $stats->draft_jobs,
            'archived_jobs' => (int) $stats->archived_jobs,
            'total_views' => (int) ($stats->total_views ?? 0),
        ]);
    }

    public function destroy(Request $request, JobListing $job)
    {
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

    public function publish(Request $request, JobListing $job)
    {
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->update([
            'status' => 'published',
            'published_at' => now(), 
        ]);

        return response()->json(['message' => 'Job published successfully']);
    }

    public function unpublish(Request $request, JobListing $job)
    {
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->update(['status' => 'draft']);
        return response()->json(['message' => 'Job moved to draft']);
    }

    public function archive(Request $request, JobListing $job)
    {
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->update(['status' => 'archived']);
        return response()->json(['message' => 'Job archived successfully']);
    }

    public function show(JobListing $jobListing): JobListingResource
    {
        $jobListing->increment('views_count');
        $jobListing->loadMissing('category');
        return new JobListingResource($jobListing);
    }

    private function applySearchAndFilters(Builder $query, array $validated): Builder
    {
        if (!empty($validated['search'])) {
            $search = trim((string) $validated['search']);
            $query->where(function (Builder $sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        if (!empty($validated['category_id'])) $query->where('category_id', (int) $validated['category_id']);
        if (!empty($validated['location'])) $query->where('location', 'like', "%".trim($validated['location'])."%");
        if (!empty($validated['work_type'])) $query->where('work_type', $validated['work_type']);
        if (!empty($validated['experience_level'])) $query->where('experience_level', $validated['experience_level']);
        if (isset($validated['salary_min'])) $query->where('salary_min', '>=', (int) $validated['salary_min']);
        if (isset($validated['salary_max'])) $query->where('salary_max', '<=', (int) $validated['salary_max']);

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
}