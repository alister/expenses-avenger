<?php
namespace AppBundle;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UserApp\API;
use UserApp\ServiceException;

class UserAppForTesting implements UserAppInterface
{
    function __construct()
    {
    }

    public function getUserId()
    {
        // internal user-ID
        return 'test-user';
    }
}
