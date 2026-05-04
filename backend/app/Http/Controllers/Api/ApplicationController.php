<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Exception;

class ApplicationController extends Controller
{
    protected $service;

    public function __construct(ApplicationService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:job_listings,id',
            'cover_letter' => 'nullable|string',
            'resume' => 'required|mimes:pdf|max:2048',
        ]);

        try {
            $user = $request->user();

            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $data = array_merge($validated, ['candidate_id' => $user->id]);

            $application = $this->service->applyToJob($data, $request->file('resume'));

            return response()->json([
                'message' => 'Application submitted successfully!',
                'data' => $application
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function myApplications()
    {
        $applications = Application::with('job')->where('candidate_id', auth()->id())->get();

        return response()->json($applications);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $this->service->changeStatus($id, $request->status, auth()->id());

        return response()->json([
            'message' => 'Application status updated to ' . $request->status,
        ]);
    }
}
