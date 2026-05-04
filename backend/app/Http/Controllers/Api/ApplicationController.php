<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

            $data = array_merge($validated, ['user_id' => $user->id]);

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
}
