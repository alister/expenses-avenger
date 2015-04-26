<?php
namespace AppBundle;

use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use AppBundle\Entity\ExpenseRepository;
use AppBundle\Entity\Expense;

class ExpenseManager
{
    /** @var array expenses */
    protected $data = array();

    public function __construct(ExpenseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function fetchSummary(array $params)
    {
        $weeklySummary = [
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
        return $weeklySummary;
    }

    private function flush()
    {
    }

    public function buildFilterQuery(array $filterParams)
    {
        $query = $this->repo->createQueryBuilder('e');

        // refactor with andX() if we add more filters
        if (isset($filterParams['startDate'], $filterParams['endDate'])) {
            $query->where('(e.createdAt >= :startDate) AND (e.createdAt <= :endDate)')
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
        #$expenses = $this->repo->findAll();
        $expensesOrdered = $this->repo->findBy(
            $filterParams,
            array('createdAt' => 'DESC'),
            $limit,
            $start
        );

        return $expensesOrdered;
        $expenses = array();
        foreach($expensesOrdered as $exp) {
            $expenses[$exp->getId()] = $exp;
        }
        #dump($expenses);#die;

        return $expenses;
    }

    public function get($id)
    {
        $expense = $this->repo->find($id);
        return $expense;

        if (!isset($this->data[$id])) {
            return false;
        }

        return $this->data[$id];
    }

    public function set(Expense $expense)
    {
        //#dump($expense);#die;
        return $this->repo->save($expense);

        if (null === $expense->getId()) {
            if (empty($this->data)) {
                $expense->setId(0);
            } else {
                end($this->data);
                $expense->setId(key($this->data) + 1);
            }
        }

        // if (null === $expense->secret) {
        //    $expense->secret = base64_encode($this->randomGenerator->nextBytes(64));
        // }

        $this->data[$expense->getId()] = $expense;
    }

    public function remove($id)
    {
        $expense = $this->get($id);
        #dump($id, $expense);die;
        if ($expense instanceof Expense) {
            $this->repo->remove($expense);
        }
        return;

        if (!isset($this->data[$id])) {
            return false;
        }

        unset($this->data[$id]);

        return true;
    }
}
