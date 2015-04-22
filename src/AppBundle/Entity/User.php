<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Expense;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
#use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * 
 * @-Serializer\ExclusionPolicy("all")
 * @-Serializer\XmlRoot("expenses")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="contractor", mappedBy="user", cascade={"persist"})
     * 
     * @-Serializer\Expose
     */
    protected $id;

    /**
     * @var AppBundle\Entity\Expense[] description
     * @ORM\OneToMany(targetEntity="Expense", mappedBy="user",cascade={"persist"})
     */
    protected $expensesOwned;

    public function __construct()
    {
        $this->expenses = array();
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function addExpense(Expense $expense)
    {
        if (!$this->getExpenses()->contains($expense)) {
            $this->getExpenses()->add($expense);
        }
    }

    public function removeCategory(Expense $expense)
    {
        if ($this->getExpenses()->contains($expense))
            $this->getExpenses()->remove($expense);
    }

    /**
     * Get the array of expenses
     *
     * @return Collection
     */
    public function getExpenses()
    {
        return $this->expensesOwned ?: $this->expensesOwned = new ArrayCollection();
    }

    /**
     * Remove expenses
     *
     * @param \AppBundle\Entity\Expense $expenses
     */
    public function removeExpense(\AppBundle\Entity\Expense $expenses)
    {
        $this->expensesOwned->removeElement($expenses);
    }
}
