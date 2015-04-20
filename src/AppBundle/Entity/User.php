<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use \AppBundle\Entity\Expense;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * 
 * @ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OneToOne(targetEntity="contractor", mappedBy="user", cascade={"persist"})
     * 
     * @Expose
     */
    protected $id;

    /**
     * Override FOS\UserBundle\Model\User as BaseUser; for username validation
     * and being able to expose to the API
     *
     * @var string
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 20,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters long"
     * )
     * 
     * @Expose
     */
    protected $username;

    /**
     * @ORM\ManyToOne(targetEntity="Expense", inversedBy="user")
     * @ORM\JoinColumn(name="expense_id", referencedColumnName="id")
     */
    protected $expenses;

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
        $this->expenses[] = $expense;
    }                  

    /**
     * Get the array of expenses
     *
     * @return array \AppBundle\Entity\Expense
     */
    public function getExpenses()
    {
        return $this->expenses;
    }
}
