<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // identifiers
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->whenLoaded('category', fn() => $this->category->name),

            // company / display fields (snake_case for API consumers, plus camelCase for frontend convenience)
            'company_name' => $this->company_name,
            'companyName' => $this->company_name,
            'company_logo' => $this->company_logo,

            // job content
            'title' => $this->title,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,

            // location / types
            'location' => $this->location,
            'work_type' => $this->work_type,
            'workType' => $this->work_type,

            // experience + salary
            'experience_level' => $this->experience_level,
            'experienceLevel' => $this->experience_level,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_formatted' => $this->formatSalary(),

            // meta
            'status' => $this->status,
            'views_count' => $this->views_count,
            'applications_count' => $this->applications_count ?? 0,
            'posted_at' => $this->published_at ? $this->published_at->diffForHumans() : 'Not published yet',
            'deadline' => $this->deadline ? $this->deadline->format('Y-m-d') : null,
            'is_owner' => $request->user() ? $this->user_id === $request->user()->id : false,
        ];
    }

    private function formatSalary(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return "{$this->salary_min} - {$this->salary_max}";
        }
        if ($this->salary_min) {
            return "From {$this->salary_min}";
        }
        return "Salary not disclosed";
    }
}
