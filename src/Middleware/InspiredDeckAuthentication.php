<?php

namespace MBLSolutions\InspiredDeckLaravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use MBLSolutions\InspiredDeck\InspiredDeck;
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
        if (!$this->authentication->isAuthenticated()) {
            throw new AuthenticationException(401);
        }

        $auth = $this->authentication->get();

        InspiredDeck::setToken($auth['access_token']);

        return $next($request);
    }

}