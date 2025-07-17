<?php
namespace App\Http\Helpers;



Trait CheckIsPostUser
{
    public function checkIsPostUserById($postId)
    {
        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $post = \App\Models\Post::find($postId);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        if ($post->user_id !== $user_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access to this post'
            ], 403);
        }

        return true;
    }
}

