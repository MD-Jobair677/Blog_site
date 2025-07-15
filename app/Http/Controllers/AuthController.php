<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {


        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        // Registration logic
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user], 201);



        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token

        ], 201);
    }

    public function login(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password

        ])) {
            // $user = User::where('email', $request->email)->first(); 
            $token = $user->createtoken('apitokan' . $user->name)->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
                'message' => 'You are not logged in'
            ], 401);
        };
    }


    public function profile(Request $request)
    {

        // Return user profile

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        } else {
            return response()->json(['user' => $user], 200);
        }
    }

    public function updateProfile(Request $request)
    {

        $user = Auth::user()->id;
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $request->name . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('avatar', $fileName, 'public');
            $user->avatar = $filePath;
        }




        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->save();
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->id;
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        Auth::logout();
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function updatePassword(Request $request)
    {
        $user  = Auth::user()->id;
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }
}

