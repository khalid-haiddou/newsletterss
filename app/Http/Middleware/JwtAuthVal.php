<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Token;


class JwtAuthVal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        try {
            if (Session::has('token')) {
                $token = new Token(Session::get('token'));

                $decodedToken = JWTAuth::decode($token);
                // dd($decodedToken);
                // $user = JWTAuth::toUser($token);
                if ($token) {
                    // dd($user);
                    return $next($request);
                }
                // if ($user) {
                //     $request->merge(['user' => $user]);
                //     return $next($request);
                // }
            }
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token is expired'], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Authorization token not found'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
