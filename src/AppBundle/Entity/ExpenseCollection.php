<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ExpenseCollection
 *
 * @Serializer\ExclusionPolicy("all")
 * @-Serializer\XmlRoot("expenseCollection")
 */
class ExpenseCollection
{
    /**
     * @var Expense[]
     * @Serializer\Expose
     */
    private $expenses;

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
