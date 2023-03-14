<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/knet_response_accept','/csreturn','masterCard_response'
    ];
	
	public function handle($request, Closure $next)
	{
		if ($request->is('gwc/*'))
		{
			return $next($request);
		}
	 
		return parent::handle($request, $next);
	}
}
