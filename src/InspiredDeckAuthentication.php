<?php

namespace MBLSolutions\InspiredDeckLaravel;

class InspiredDeckAuthentication
{
    /** @var string $name */
    public $name;

    /** @var string $email */
    public $email;

    /** @var string $role */
    public $role;

    /**
     * Inspired Deck Authentication
     */
    public function __construct()
    {
        $auth = (new Authentication())->get();

        $this->loadUser($auth['user']);
    }

    /**
     * Load User
     *
     * @param array $user
     * @return InspiredDeckAuthentication
     */
    private function loadUser(array $user): self
    {
        $this->name = $user['name'] ?? null;
        $this->email = $user['email'] ?? null;
        $this->role = $user['role'] ?? null;

        return $this;
    }

    /**
     * Get the Users Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Users Email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the Users Role
     *
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

}