<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'companyName' => $this->company_name,
            'company_logo' => $this->company_logo,
            'description' => $this->description,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'location' => $this->location,
            'workType' => $this->work_type,
            'experienceLevel' => $this->experience_level,
            'salaryMin' => $this->salary_min,
            'salaryMax' => $this->salary_max,
            'salaryCurrency' => $this->salary_currency,
            'publishedAt' => optional($this->published_at)->toIso8601String(),
            'viewsCount' => $this->views_count,
            'applicationsCount' => $this->applications_count,
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
