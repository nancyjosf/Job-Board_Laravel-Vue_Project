<?php

namespace App\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    public function create(array $data)
    {
        return Application::create($data);
    }

    public function hasAlreadyApplied($candidateId, $jobId)
    {
        return Application::where('candidate_id', $candidateId)
                          ->where('job_id', $jobId)
                          ->exists();
    }
}
