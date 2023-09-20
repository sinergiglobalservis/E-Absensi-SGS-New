<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Models\User\{AccessToken, User};

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check authorization has exist
        if (!$request->header('Authorization')) {
            return response()->json([
                "status" => 0,
                "message" => "Access Token mohon disematkan",
                "data" => null,
                "code" => 401
            ], 401);
        }

        list($type_access, $token) = explode(" ", $request->header('Authorization'));

        // re-set request appAccess
        $request->merge([
            'auth' => [
                'type' => $type_access,
                'token' => $token
            ]
        ]);

        try {
            // check activation token
            if ($email = (new AccessToken())->checkToken($request)) {
                // get user data from token
                $user = (new User)->where('email', $email)->first();

                $request->merge([
                    'userAccess' => $user
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => $e->getMessage(),
                "data" => null,
                "code" => 401
            ], 401);
        }

        return $next($request);
    }
}
