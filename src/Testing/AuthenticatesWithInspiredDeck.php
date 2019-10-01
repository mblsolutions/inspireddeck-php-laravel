<?php

namespace MBLSolutions\InspiredDeckLaravel\Testing;

use MBLSolutions\InspiredDeckLaravel\Authentication;

trait AuthenticatesWithInspiredDeck
{

    /**
     * Simulate authentication with Inspired Deck OAuth Authentication
     *
     * @param string|null $role
     * @return array
     */
    protected function authenticateWithInspiredDeck(string $role = null): array
    {
        $key = $this->getInspiredDeckAuthentication()->sessionKey;

        session()->put($key, [
            'token_type' => 'Bearer',
            'expires_in' => 31622400,
            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmOGMwNDAxZDAy',
            'refresh_token' => 'def5020002eca9ac7875d5d800c195024d7fb702535c0d30a0',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'programme_manager'
            ]
        ]);

        return session()->get($key);
    }

    /**
     * Get Authentication
     *
     * @return array|null
     */
    protected function getAuthetnication(): ?array
    {
        $auth = session()->get($this->getInspiredDeckAuthentication()->sessionKey);

        if ($auth === null) {
            return $this->authenticateWithInspiredDeck();
        }

        return $auth;
    }

    /**
     * Get the Inspired Deck Authentication Class
     *
     * @return Authentication
     */
    private function getInspiredDeckAuthentication(): Authentication
    {
        return new Authentication;
    }

}
