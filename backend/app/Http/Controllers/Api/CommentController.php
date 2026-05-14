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
            'user_id' => auth()->id(),
            'job_listing_id' => $validated['job_listing_id'],
            'content' => $validated['content'],
        ]);
        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user:id,name'),
        ]);

    }
    public function index($jobId)
    {
        $comments = Comment::with('user:id,name')->where('job_listing_id', $jobId)->latest()->get();
        return response()->json($comments);
    }
    public function destroy($id)
    {
        $comment=Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->delete();
        return response()->json(['message'=>'Comment deleted sucessfully']);
    }
}
