<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 👤 Show profile
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    // ✏️ Update profile
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'nullable|string|max:20',
            'skills' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

       
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles/images', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('profiles/cvs', 'public');
            $validated['cv'] = $cvPath;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}