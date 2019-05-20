<?php

namespace MBLSolutions\InspiredDeckLaravel;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Session;
use MBLSolutions\InspiredDeck\Exceptions\AuthenticationException;

class Authentication extends \MBLSolutions\InspiredDeck\Authentication
{
    /** @var string $key */
    public $sessionKey;

    /**
     * Inspired Deck Authentication
     */
    public function __construct()
    {
        parent::__construct();

        $this->sessionKey = config('inspireddeck.session');
    }

    /**
     * Get the currently Authenticated User
     *
     * @return mixed
     */
    public function get()
    {
        return Session::get($this->sessionKey, false);
    }

    /**
     * Authenticate the User using OAuth Password Grant
     *
     * @param string $username
     * @param string $password
     * @throws GuzzleException
     * @throws AuthenticationException
     */
    public function login($username, $password)
    {
        $response = $this->password(
            config('inspireddeck.client_id'),
            config('inspireddeck.secret'),
            $username,
            $password
        );

        $this->store($response);
    }

    /**
     * Remove OAuth session
     *
     * @return void
     */
    public function logout()
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Store the OAuth session
     *
     * @param array $auth
     * @return void
     */
    private function store(array $auth)
    {
        Session::put($this->sessionKey, $auth);
    }

}