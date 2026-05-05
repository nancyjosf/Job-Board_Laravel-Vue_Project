<?php

namespace App\Services;

use App\Repositories\ApplicationRepository;
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
        if ($this->repository->hasAlreadyApplied($data['candidate_id'], $data['job_id'])) {
            throw new Exception("You have already applied for this job.");
        }

        if ($file) {
            $path = $file->store('resumes', 'public');
            $data['resume_path'] = $path;
        }

        return $this->repository->create($data);
    }

    public function changeStatus($applicationId, $status, $userId)
    {
        return $this->repository->updateStatus($applicationId, $status);
    }

    public function cancelApplication($applicationId, $candidateId)
    {
        return $this->repository->delete($applicationId);
    }
}
