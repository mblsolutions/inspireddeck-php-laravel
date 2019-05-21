<?php

namespace MBLSolutions\InspiredDeckLaravel;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getName()
 * @method static string getEmail()
 * @method static string getRole()
 *
 * @see InspiredDeckAuthentication
 */
class InspiredDeckAuthFacade extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return InspiredDeckAuthentication::class;
    }
}