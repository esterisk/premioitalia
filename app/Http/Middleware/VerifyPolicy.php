<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
use App\Services\DelosCentral;

class VerifyPolicy extends BaseVerifier
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
		if (\Auth::user() && !\Auth::user()->policyApproved()) {
			$url = DelosCentral::urlVerificaPrivacy($request->url());
			if ($url) return \Redirect::to($url);
        }

        return $next($request);
    }
    
}
