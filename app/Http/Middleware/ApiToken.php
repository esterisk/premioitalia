<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
use App\User;

class ApiToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
    
    public function handle($request, Closure $next, $guard = null)
    {
    	if (!$request->token 
    		|| !($user = User::whereToken($request->token)->first())
    		|| !$user->isAdmin()
    	) {
    		abort(401);
    	}
        return $next($request);
    }
    
}
