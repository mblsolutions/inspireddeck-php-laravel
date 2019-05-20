<?php

namespace MBLSolutions\InspiredDeckLaravel\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use MBLSolutions\InspiredDeck\InspiredDeck;

class LoadInspiredDeckConfig
{
    /** @var InspiredDeck $config */
    protected $config;

    /**
     * Create a new middleware Instance
     *
     * @param InspiredDeck $deck
     */
    public function __construct(InspiredDeck $deck)
    {
        $this->config = $deck;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

}