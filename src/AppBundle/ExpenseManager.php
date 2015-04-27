<?php
namespace AppBundle;

use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use AppBundle\Entity\ExpenseRepository;
use AppBundle\Entity\Expense;

class ExpenseManager
{
    private $repo;
    private $userApp;

    /** @var array expenses */
    protected $data = array();

    private $userId;

    public function __construct(ExpenseRepository $repo, UserAppInterface $userApp)
    {
        $this->repo    = $repo;
        $this->userApp = $userApp;
    }

    public function calcSummary($params)
    {
        return $this->repo->summaryByWeek($this->userApp->getUserId());
        //return $this->CalcSummaryTestData($params);
    }

    public function CalcSummaryTestData($params = [])
    {
        // @todo params to narrow down
        return [
            [   'weekStart' => '2015-04-13',
                'weekEnd'   => '2015-04-19',
                'total'     => number_format(68, 2),
                'avgDay'    => number_format(round(68/7, 2), 2),
            ],
            // @todo - go and calculate the data
            [   'weekStart' => '2015-04-06',
                'weekEnd'   => '2015-04-12',
                'total'     => number_format(22, 2),
                'avgDay'    => number_format(round(22/7, 2), 2),
            ],
            [   'weekStart' => '2015-03-30',
                'weekEnd'   => '2015-04-05',
                'total'     => number_format(15, 2),
                'avgDay'    => number_format(round(15/7, 2), 2),
            ],
        ];
    }

    private function flush()
    {
    }

    public function buildFilterQuery(array $filterParams)
    {
        $query = $this->repo->createQueryBuilder('e');

        // refactor with andX() if we add more filters
        if (isset($filterParams['startDate'], $filterParams['endDate'])) {
            $where = $query->where('(e.createdAt >= :startDate) AND (e.createdAt <= :endDate)')
                ->setParameter('startDate', $filterParams['startDate'])
                ->setParameter('endDate', $filterParams['endDate']);
        }

        if (isset($filterParams['limit'])) {
            $query->setMaxResults($filterParams['limit']);
        }

        if (isset($filterParams['offset'])) {
            $query->setFirstResult($filterParams['offset']);
        }

        return $query->getQuery();
    }

    public function fetchFiltered(array $filterParams)
    {
        $query = $this->buildFilterQuery($filterParams);
        $x = $query->getResult();
        return $x;
    }

    public function fetch($start = 0, $limit = 25, $filterParams = [])
    {
        $expensesOrdered = $this->repo->findBy(
            $filterParams,
            array('createdAt' => 'DESC'),
            $limit,
            $start
        );

        return $expensesOrdered;
    }

    public function get($id)
    {
        $expense = $this->repo->find($id);
        //#dump($expense);
        return $expense;
    }

    public function set(Expense $expense)
    {
        $expense->setUser($this->userApp->getUserId());
        //#dump($expense);
        return $this->repo->save($expense);
    }

    public function remove($id)
    {
        $expense = $this->get($id);
        if ($expense instanceof Expense) {
            $this->repo->remove($expense);
        }
        return;
    }
}
