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

    public function updateStatus($applicationId, $status)
    {
        return Application::where('id', $applicationId)->update(['status' => $status]);
    }

    public function delete($applicationId)
    {
        
        return Application::where('id', $applicationId)->delete();
    }
}
