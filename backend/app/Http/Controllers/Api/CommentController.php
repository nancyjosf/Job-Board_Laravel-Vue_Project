<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_listing_id' => 'required|exists:job_listings,id',
            'content' => 'required|string|max:255'
        ]);
        $comment = Comment::create([
            'user_id'=>auth()->id(),
            'job_listing_id' => $validated['job_listing_id'],
            'content' => $validated['content'],
        ]);
        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user:id,name'),
        ]);

    }
}
