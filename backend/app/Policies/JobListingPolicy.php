<?php

namespace App\Policies;

use App\Models\JobListing;
use App\Models\User;

class JobListingPolicy
{
    public function view(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function update(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function delete(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function publish(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function unpublish(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function archive(User $user, JobListing $job): bool
    {
        return $user->id === $job->user_id;
    }
}