<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Application;
use App\Models\JobListing;
use App\Http\Controllers\Controller;
class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'users' => User::count(),
            'jobs' => JobListing::count(),
            'applications' => Application::count(),
        ]);
    }
    public function approveJob($id)
    {
        $job = JobListing::findOrFail($id);
        $job->approval_status = 'approved';
        $job->save();
        return response()->json(['message' => 'Job approved successfully']);
    }
    public function rejectJob($id)
    {
        $job = JobListing::findOrFail($id);
        $job->approval_status= 'rejected';
        $job->save();
        return response()->json(['message' => 'Job rejected successfully']);
    }
}