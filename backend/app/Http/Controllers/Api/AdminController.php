<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Application;
use App\Models\JobListing;
use App\Http\Controllers\Controller;
use App\Models\Comment;
class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'users' => User::count(),
            'jobs' => JobListing::count(),
            'applications' => Application::count(),
            'comments' => Comment::count(),
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
        $job->approval_status = 'rejected';
        $job->save();
        return response()->json(['message' => 'Job rejected successfully']);
    }
    public function users()
    {
        $users = User::all();
        return response()->json($users);

    }
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
    public function analytics(){
        return response()->json([

        'total_users' => User::count(),

        'total_jobs' => JobListing::count(),

        'total_applications' => Application::count(),

        'total_comments' => Comment::count(),

        'approved_jobs' => JobListing::where('approval_status', 'approved')->count(),

        'rejected_jobs' => JobListing::where('approval_status', 'rejected')->count(),

        'pending_jobs' => JobListing::where('approval_status', 'pending')->count(),

    ]);
    }
}