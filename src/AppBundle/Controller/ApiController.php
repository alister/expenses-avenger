<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
#use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    /**
     * @Route("/spike", name="api_spiketest")
     */
    public function SpikeTestAction()
    {
        $data =  array(
            'appname' => 'expenses'
        );

        $response = new JsonResponse($data);
        #$response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
