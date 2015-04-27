<?php
namespace AppBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\UserApp;

class FilterConfiguratorForTest
{
    protected $em;
    protected $request;
    protected $uApp;

    public function __construct(ObjectManager $em, Request $request, UserApp $uApp)
    {
        $this->em      = $em;
        $this->request = $request;
        $this->uApp    = $uApp;
    }

    public function onKernelRequest()
    {
        $filter = $this->em->getFilters()->disable('user_filter');
    }
}
