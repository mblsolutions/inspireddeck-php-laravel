<?php

namespace MBLSolutions\InspiredDeckLaravel;

interface InspiredDeckAuth
{

    /**
     * Get the currently Authenticated User
     *
     * @return mixed
     */
    public function get(): array;
}