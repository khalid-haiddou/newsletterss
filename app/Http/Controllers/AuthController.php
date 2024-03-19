<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return redirect('/')->with('success', 'Registration successful. Please log in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/')
                ->withErrors($validator)
                ->withInput();
        }

        if (Auth::attempt($credentials)) {
            $token = JWTAuth::fromUser(Auth::user());
            // dd($token);
            if ($token) {
                Session::put('token', $token);
                return redirect('/dashboard');
            } else {
                return response()->json(['error' => 'Failed to create token'], Response::HTTP_BAD_REQUEST);
            }
        }

        return redirect('/')
            ->withErrors(['error' => 'Invalid credentials'])
            ->withInput();
    }

    public function refreshToken(Request $request)
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            return response()->json(compact('token'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token could not be refreshed'], Response::HTTP_UNAUTHORIZED);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        Session::forget('token');
        return redirect('/')->with('success', 'Logged out successfully');
    }
}
