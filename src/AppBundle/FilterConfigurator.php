<?php
namespace AppBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\UserApp;

class FilterConfigurator
{
    protected $em;
    protected $request;
    protected $uApp;

    public function __construct(ObjectManager $em, Request $request, UserAppInterface $uApp)
    {
        $this->em      = $em;
        $this->request = $request;
        $this->uApp    = $uApp;
    }

    public function onKernelRequest()
    {
        if ($userId = $this->uApp->getUserId($this->request)) {
            $filter = $this->em->getFilters()->enable('user_filter');
            $filter->setParameter('id', $userId);
        }
    }
}
