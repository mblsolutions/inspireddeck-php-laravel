<?php

namespace MBLSolutions\InspiredDeckLaravel\Exceptions;

class InvalidUserRoleException extends AuthenticationException
{

    /** {@inheritDoc} */
    protected $message = 'Your user role does not have permission for this action';

}