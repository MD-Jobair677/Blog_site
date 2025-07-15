<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


trait SlugGeneretor
{
    public function generateSlug($title)
    {
        // Generate a slug from the title
        $slug = Str::slug($title, '-');

        // Check if the slug already exists in the database
        $count = \App\Models\Post::where('slug', $slug)->count();

        // If it exists, append a number to make it unique
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        return $slug;
    }



    public function success($code, $message, $data = null)
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }



    public function error($code, $message, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }



    function validateRequest($code,$request, $rules)
    {
        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            return $this->error($code, 'Validation failed', $validator->errors());
        }

        return null; 
    }






}
