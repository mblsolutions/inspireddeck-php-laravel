<?php

namespace MBLSolutions\InspiredDeckLaravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use MBLSolutions\InspiredDeckLaravel\Authentication;
use MBLSolutions\InspiredDeckLaravel\Exceptions\AuthenticationException;

class InspiredDeckAuthentication
{
    /** @var Authentication $authentication */
    private $authentication;

    public function __construct()
    {
        $this->authentication = new Authentication;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has($this->authentication->sessionKey)) {
            throw new AuthenticationException(401);
        }

        return $next($request);
    }

}