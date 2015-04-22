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

    private function flush()
    {
        #file_put_contents($this->cacheDir . '/sf_note_data', serialize($this->data));
    }

    public function fetch($start = 0, $limit = 25)
    {
        #$expenses = $this->repo->findAll();
        $expensesOrdered = $this->repo->findBy(
            array(),
            array('createdAt' => 'DESC'),
            $limit,
            $start
        );

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
        dump($expense);#die;
        return $expense;

        if (!isset($this->data[$id])) {
            return false;
        }
        return $this->data[$id];
    }

    public function set(Expense $expense)
    {
dump($expense);#die;
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
dump($this->data);
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
