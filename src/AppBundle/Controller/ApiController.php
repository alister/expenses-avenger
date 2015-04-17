<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\ExpenseLine;
#use FOS\RestBundle\Controller\Annotations\RequestParam;
#use FOS\RestBundle\Request\ParamFetcher;

class ApiController extends Controller
{

    /**
     * Return demo data for API test - frontend
     *
     * @ApiDoc(
     *   description = "Return one line of expense data by primary key",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when the user does not have permission",
     *     404 = "Returned when the ID is not found"
     *   }
     * )
     * 401: [Unauthorized]
     * 
     * @ParamConverter("line", class="AppBundle:ExpenseLine", options={"line" = "id"})
     *  
     * @return View
     */
    public function getExpenselineAction(ExpenseLine $line)
    {
        $view = View::create();
        $view->setData($line)
            ->setStatusCode(200);
        #$view->setTemplate('AppBundle:Demo:index.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Return demo data for API test - frontend
     *
     * @ApiDoc(
     *   description = "Return the example expenses data",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when the user does not have permission",
     *     404 = "Returned when the data is not found...?"
     *   }
     * )
     * 401: [Unauthorized]
     *  
     * @return View
     */
    public function getDemoExpensesAction()
    {
        $data = [
            [ 'name' => 'Nexus 4',
             'snippet' => 'Just available to be upgraded to Lolliop, 5.1 - Nexus 4.',
             'age' => 2 ],
            [ 'name' => 'Nexus S',
             'snippet' => 'Fast just got faster with Nexus S.',
             'age' => 1 ],
            [ 'name' => 'Motorola XOOM™ with Wi-Fi',
             'snippet' => 'The Next, Next Generation tablet.',
             'age' => 2 ],
            [ 'name' => 'MOTOROLA XOOM™',
             'snippet' => 'The Next, Next Generation tablet.',
             'age' => 3 ],
        ];
        $view = View::create();
        $view->setData($data)
            ->setStatusCode(200);
        #$view->setTemplate('AppBundle:Demo:index.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

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
