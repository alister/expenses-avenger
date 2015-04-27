<?php
namespace AppBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Matt Drollette <matt@drollette.com>
 */
interface InitializableControllerInterface
{
    public function initialize(Request $request, SecurityContextInterface $security_context);
}
