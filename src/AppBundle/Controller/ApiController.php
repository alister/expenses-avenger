<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
#use FOS\RestBundle\Controller\Annotations\RequestParam;
#use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Controller\Traits;
use AppBundle\Entity\Expense;
use AppBundle\Entity\User;

class ApiController extends Controller
{
    //#use Traits\SpikeTrait; 

    /*
     * Return one line of expense data by its uniq id/key
     * 
     * This will include the user that created it, but you have to know 
     * which one you're getting.
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
     * @ParamConverter("line", class="AppBundle:Expense", options={"line" = "id"})
     *  
     * @return View
     */
    // public function getExpenseAction(Expense $line)
    // {
    //     $view = View::create();
    //     $view->setData($line)
    //         ->setStatusCode(200);
    //     #$view->setTemplate('AppBundle:Demo:index.html.twig');
    //     return $this->get('fos_rest.view_handler')->handle($view);
    // }

    /**
     * Return lines of expense data for a user
     * 
     * @ApiDoc(
     *   description = "Return multiple lines of expense data for a user",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing lines.")
     * @Annotations\QueryParam(name="limit", requirements="\d+",  default="25", description="How many lines to return.")
     * 
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return View
     */
    public function getExpensesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        #return $this->container->get('acme_blog.page.handler')->all($limit, $offset);
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Expense');
        $x = $repository->findBy(array(), null, $limit, $offset);
        return($x);die;

        $view = View::create();
        $view->setData($line)
            ->setStatusCode(200);
        #$view->setTemplate('AppBundle:Demo:index.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Post Collection action
     *
     * @ApiDoc(
     *   description = "Create a full expense report, with embedded expense lines",
     *   input="AppBundle\Entity\User",
     *   statusCodes = {
     *     201 = "Created successfully",
     *     401 = "Returned when the user does not have permission",
     *     404 = "Returned when the ID is not found"
     *   }
     * )
     * 
     * @var User    $userId
     * @var Request $request
     * 
     * @return View|array
     */
    public function cpostExpenseAction(User $userId, Request $request)
    {
        $entity = new Expense();
        $form = $this->createForm(new ExpenseType(), $entity);
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


    /**
     * Single expense Post action
     *
     * @ApiDoc(
     *   description = "Create a new line for the expense report for the given user",
     *   input="AppBundle\Entity\Expense",
     *   statusCodes = {
     *     201 = "Created successfully",
     *     401 = "Returned when the user does not have permission",
     *     404 = "Returned when the ID is not found"
     *   }
     * )
     * 
     * @var Expense $data
     * @var Request $request
     * 
     * @return View|array
     */
    public function postExpenseAction(Expense $data, Request $request)
    {
        $entity = new Expense();
        $form = $this->createForm(new ExpenseType(), $entity);
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
    }}
