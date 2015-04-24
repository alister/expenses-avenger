<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Expense;
use AppBundle\Entity\ExpenseCollection;
use AppBundle\Entity\User;
use AppBundle\Form\ExpenseType;
use DateTime;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExpenseController extends FOSRestController
{
    /**
     * return \AppBundle\ExpenseManager
     */
    public function getExpenseManager()
    {
        return $this->get('app.expense_manager');
    }

    /**
     * List all expenses.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing expenses.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="25", description="How many expenses to return.")
     *
     * @Annotations\View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getExpensesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $start = (null == $offset) ? 0 : $offset;
        $limit = (integer)$paramFetcher->get('limit');

        $expenses = $this->getExpenseManager()->fetch($start, $limit);
        //#$expenses = $this->testExpenses();

        return $expenses;
        return ['data' => ['expenses' => $expenses]];
        return new ExpenseCollection($expenses, $offset, $limit);
    }

    public function testExpenses()
    {
        $this->user = new User();
        $this->user->setUsername('username')
            ->setPlainPassword('password')
            ->setEmail('email@example.org')
            ->setEnabled(true)
            ->setRoles(['role'])
        ;
        $expenses[] = $this->createExpense(
            new DateTime('2015-04-20T09:18:01+0100'),
            '5.98',
            'breakfast',
            'yep, I went out for a cooked breakfast'
        );
        $expenses[] = $this->createExpense(
            new DateTime('2015-04-20T13:56:07+0100'),
            '12.99',
            'dinner',
            'hungry'
        );
        return $expenses;
    }

    public function createExpense(DateTime $date, $amount, $desc, $comment)
    {
        $expense = new Expense();
        $expense
            ->setUser($this->user)
            ->setCreatedAt($date)
            ->setDescription($desc)
            ->setAmount($amount)
            ->setComment($comment);
        return $expense;
    }

    /**
     * Get a single expense.
     *
     * @ApiDoc(
     *   output = "AppBundle\Model\Expense",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the expense is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="expense")
     *
     * @param Request $request the request object
     * @param int     $id      the expense id
     *
     * @return array
     *
     * @throws NotFoundHttpException when expense not exist
     */
    public function getExpenseAction(Request $request, $id)
    {
        $expense = $this->getExpenseManager()->get($id);
        if (!$expense) {
            throw $this->createNotFoundException("Expense does not exist.");
        }

        $view = new View($expense);
        $group = $this->container->get('security.context')->isGranted('ROLE_API') ? 'restapi' : 'standard';
        $view->getSerializationContext()->setGroups(array('Default', $group));

        return $view;
    }

    /**
     * Presents the form (as HTML) to use to create a new expense.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return FormTypeInterface
     */
    public function newExpenseAction()
    {
        return $this->createForm(new ExpenseType());
    }

    /**
     * Creates a new expense from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\ExpenseType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "AppBundle:Expense:newExpense.html.twig",
     *   statusCode = Codes::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|RouteRedirectView
     */
    public function postExpensesAction(Request $request)
    {
        $expense = new Expense();
        //$this->handleExpensesData($request, $expense);
        $e = $this->get('request')->request->all();
        $dateName = 'created_at';
        if (!isset($e[$dateName], $e['amount'], $e['description'], $e['comment'])) {
            throw new \Exception("Error Processing Request");
        }
        $expense->setcreatedAt($e[$dateName]);
        $expense->setDescription($e['description']);
        $expense->setAmount($e['amount']);
        $expense->setComment($e['comment']);

        $this->getExpenseManager()->set($expense);
        return $this->routeRedirectView('get_expense', array('id' => $expense->getId()));

        return array(
            'form' => $form
        );
        ////////////////////////////////////////
        //#return $this->handlePostForm($request);
    }

    public function handlePostForm($request)
    {
        $expense = new Expense();
        $form = $this->createForm(new ExpenseType(), $expense);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getExpenseManager()->set($expense);
            return $this->routeRedirectView('get_expense', array('id' => $expense->getId()));
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Presents the form to use to update an existing expense.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the expense is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param int     $id      the expense id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when expense not exist
     */
    public function editExpenseAction(Request $request, $id)
    {
        $expense = $this->getExpenseManager()->get($id);
        if (false === $expense) {
            throw $this->createNotFoundException("Expense does not exist.");
        }

        $form = $this->createForm(new ExpenseType(), $expense);

        return $form;
    }

    /**
     * Update existing expense from the submitted data or create a new expense at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\ExpenseType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="AppBundle:Expense:editExpense.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the expense id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when expense not exist
     */
    public function putExpenseAction(Request $request, $id)
    {
        $expense = $this->getExpenseManager()->get($id);
        if (false === $expense) {
            $expense = new Expense();
            $expense->id = $id;
            $statusCode = Codes::HTTP_CREATED;
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }

        $e = $this->get('request')->request->all();
        $dateName = 'created_at';
        if (isset($e[$dateName])) {
            $expense->setcreatedAt($e[$dateName]);
        }
        if (isset($e['description'])) {
            $expense->setDescription($e['description']);
        }
        if (isset($e['amount'])) {
            $expense->setAmount($e['amount']);
        }
        if (isset($e['comment'])) {
            $expense->setComment($e['comment']);
        }

        $this->getExpenseManager()->set($expense);
        return $this->routeRedirectView('get_expense', array('id' => $expense->getId()), $statusCode);
        #$form = $this->createForm(new ExpenseType(), $expense);
        // $form->submit($request);
        // if ($form->isValid()) {
        //}
        return $form;
    }

    /**
     * Removes a expense.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the expense id
     *
     * @return RouteRedirectView
     */
    public function deleteExpenseAction(Request $request, $id)
    {
        $this->getExpenseManager()->remove($id);
        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        return $this->routeRedirectView('get_expenses', array(), Codes::HTTP_NO_CONTENT);
    }

    /**
     * Removes a expense. Alias for deleteExpenseAction
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the expense id
     *
     * @return RouteRedirectView
     */
    public function removeExpenseAction(Request $request, $id)
    {
        return $this->deleteExpenseAction($request, $id);
    }
}
