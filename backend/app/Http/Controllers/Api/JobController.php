<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    /**
     * عرض قائمة الوظائف مع الفلترة والترتيب
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // 1. التحقق من البيانات القادمة من الطلب
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer'],
            'location' => ['nullable', 'string', 'max:120'],
            'work_type' => ['nullable', 'in:remote,onsite,hybrid'],
            'experience_level' => ['nullable', 'in:junior,mid,senior,lead'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'sort' => ['nullable', 'in:newest,oldest,salary_desc,salary_asc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        // 2. بناء الاستعلام الأساسي (مع جلب القسم لتحسين الأداء)
        $query = JobListing::query()
            ->with('category'); 

        // 3. تطبيق الفلاتر والبحث
        $query = $this->applySearchAndFilters($query, $validated);

        // 4. تطبيق الترتيب
        $this->applySorting($query, $validated['sort'] ?? null);

        // 5. تنفيذ الترقيم (Pagination)
        $perPage = (int) ($validated['per_page'] ?? 12);
        $jobs = $query->paginate($perPage)->withQueryString();

        return JobListingResource::collection($jobs);
    }

    /**
     * عرض تفاصيل وظيفة واحدة
     */
    public function show(JobListing $jobListing): JobListingResource
    {
        // التأكد من أن الوظيفة منشورة (إذا كان لديك Scope باسم public يمكنك استخدامه)
        // abort_unless($jobListing->status === 'published', 404);

        // زيادة عدد المشاهدات
        $jobListing->increment('views_count');

        // تحميل القسم إذا لم يكن محملًا
        $jobListing->loadMissing('category');

        return new JobListingResource($jobListing);
    }

    /**
     * دالة خاصة لتطبيق البحث والفلترة
     */
    private function applySearchAndFilters(Builder $query, array $validated): Builder
    {
        // البحث بالنص (q)
        if (!empty($validated['q'])) {
            $q = trim((string) $validated['q']);
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%");
            });
        }

        // الفلترة بالتصنيف (Category) - السطر اللي بيحل مشكلتك
        if (!empty($validated['category_id'])) {
            $query->where('category_id', (int) $validated['category_id']);
        }

        // الفلترة بالموقع
        if (!empty($validated['location'])) {
            $loc = trim((string) $validated['location']);
            $query->where('location', 'like', "%{$loc}%");
        }

        // نوع العمل (Remote, etc)
        if (!empty($validated['work_type'])) {
            $query->where('work_type', (string) $validated['work_type']);
        }

        // مستوى الخبرة
        if (!empty($validated['experience_level'])) {
            $query->where('experience_level', (string) $validated['experience_level']);
        }

        // الحد الأدنى للراتب
        if (isset($validated['salary_min'])) {
            $query->where(function (Builder $sub) use ($validated) {
                $sub->whereNull('salary_min')
                    ->orWhere('salary_min', '>=', (int) $validated['salary_min']);
            });
        }

        // الحد الأقصى للراتب
        if (isset($validated['salary_max'])) {
            $query->where(function (Builder $sub) use ($validated) {
                $sub->whereNull('salary_max')
                    ->orWhere('salary_max', '<=', (int) $validated['salary_max']);
            });
        }

        return $query;
    }

    /**
     * دالة خاصة للترتيب
     */
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