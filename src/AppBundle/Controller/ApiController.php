<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
#use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
#use FOS\RestBundle\Request\ParamFetcher;

class ApiController extends Controller
{

    /**
     * Return demo data for an API test
     *
     * @ApiDoc(
     *   description = "Return the demo data",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the appname is not found...?"
     *   }
     * )
     *
     * @return View
     */
    public function getSpikeAction()
    {
        $data = [
            'appname' => 'expenses',
        ];
        $view = View::create();
        $view->setData($data)
            ->setStatusCode(200);
        #$view->setTemplate('AppBundle:Demo:index.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
