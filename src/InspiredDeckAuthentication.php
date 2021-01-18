<?php

namespace MBLSolutions\InspiredDeckLaravel;

class InspiredDeckAuthentication
{
    /** @var array $user */
    protected $user;

    /** @var InspiredDeckAuth $authentcation */
    protected $authentication;

    /**
     * Inspired Deck Authentication
     *
     * @param InspiredDeckAuth $authentication
     */
    public function __construct(InspiredDeckAuth $authentication = null)
    {
        $this->authentication = $authentication ?: new Authentication;

        $this->loadUser();
    }

    /**
     * Get the Users Name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->user['name'];
    }

    /**
     * Returns display name including brand
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        $brand = $this->getBrand();

        return $brand ? $brand['name'].' | '.$this->getName() : $this->getName();
    }

    /**
     * Get the Users Email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->user['email'];
    }

    /**
     * Get the Users Role
     *
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->user['role'];
    }

    /**
     * Get the Users Brand
     *
     * @return array|null
     */
    public function getBrand(): ?array
    {
        return $this->user['brand'] ?? null;
    }

    /**
     * Check if user has role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->getRole() === $role;
    }

    /**
     * Check if user has one of the supplied roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasRoles(array $roles): bool
    {
        return in_array($this->getRole(), $roles, true);
    }

    /**
     * Load User
     *
     * @param string $key
     * @return InspiredDeckAuthentication
     */
    private function loadUser($key = 'user'): self
    {
        $auth = $this->authentication->get();

        $this->user = $key ? $auth[$key] : $auth;

        return $this;
    }



}