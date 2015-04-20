<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ExpenseLine;
use AppBundle\Entity\User;
#use FOS\RestBundle\Controller\Annotations\RequestParam;
#use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Controller\Traits;

class ApiController extends Controller
{
    use Traits\SpikeTrait;

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
    public function getExpenseAction(ExpenseLine $line)
    {
        $view = View::create();
        $view->setData($line)
            ->setStatusCode(200);
        #$view->setTemplate('AppBundle:Demo:index.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    // public function postFredAction(Request $request, User $id) // User $line, ExpenseLine
    // {
    //     $line = [];
    //     $view = View::create();
    //     $view->setData($line)
    //         ->setStatusCode(201);
    //     #$view->setTemplate('AppBundle:Demo:index.html.twig');
    //     return $this->get('fos_rest.view_handler')->handle($view);
    // }

    /**
     * Collection post action
     *
     * @ApiDoc(
     *   description = "Create one line of expense data by primary key",
     *   statusCodes = {
     *     201 = "Created successfully",
     *     401 = "Returned when the user does not have permission",
     *     404 = "Returned when the ID is not found"
     *   }
     * )
     * 
     * @var User        $id
     * @var ExpenseLine $data
     * @var Request     $request
     * 
     * @return View|array
     */
    public function postExpenseAction(User $id, ExpenseLine $data, Request $request)
    {
        $entity = new ExpenseLine();
        $form = $this->createForm(new ExpenseLineType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                    $this->generateUrl(
                        'get_organisation',
                        array('id' => $entity->getId())
                        ),
                    Codes::HTTP_CREATED
                    );
        }

        return array(
            'form' => $form,
        );
    }
}
