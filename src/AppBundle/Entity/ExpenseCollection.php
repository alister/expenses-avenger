<?php
namespace AppBundle\Entity;

class ExpenseCollection
{
    /**
     * @var Expense[]
     */
    public $expenses;

    /**
     * @var integer
     */
    public $offset;

    /**
     * @var integer
     */
    public $limit;

    /**
     * @param Expense[] $expenses
     * @param integer   $offset
     * @param integer   $limit
     */
    public function __construct($expenses = array(), $offset = null, $limit = null)
    {
        $this->expenses = $expenses;
        $this->offset = $offset;
        $this->limit = $limit;
    }
}
