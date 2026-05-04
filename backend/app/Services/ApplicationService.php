<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
use Illuminate\Support\Facades\Storage;
use Exception;

class ApplicationService
{
    protected $repository;

    public function __construct(ApplicationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function applyToJob(array $data, $file)
    {
        if ($this->repository->hasAlreadyApplied($data['user_id'], $data['job_id'])) {
            throw new Exception("You have already applied for this job.");
        }

        if ($file) {
            $path = $file->store('resumes', 'public');
            $data['resume'] = $path;
        }

        return $this->repository->create($data);
    }
}
