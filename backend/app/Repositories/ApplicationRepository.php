<?php

namespace App\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    
    public function create(array $data)
    {
        return Application::create($data);
    }

   
    public function hasAlreadyApplied($userId, $jobId)
    {
        return Application::where('user_id', $userId)
                          ->where('job_id', $jobId)
                          ->exists();
    }
}
