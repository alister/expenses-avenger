<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Expense - one line of expenses for a (connected) user
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ExpenseRepository")
 * @ORM\Table(name="expenses")
 *
 * @ExclusionPolicy("all")
 */
class Expense
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * Bidirectional - Many Expenses are owned by one user (OWNING SIDE)
     * 
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="expensesOwned")
     *
     * @Expose
     */
    private $user;

    /**
     * Date of expense (input by user, default to today)
     * 
     * @var \DateTime
     *
     * @ORM\Column(name="when", type="datetime")
     */
    private $when;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    // =================================================================

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Expenses
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    // {{{ primary data stored

    /**
     * Set date of expense
     *
     * @param datetime $when
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date of expense
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ExpenseLine
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set amount
     *
     * To avoid problems with floats, we store it in the model as a string
     * and then it's co-erced as required. In the DB it's a DECIMAL
     * 
     * @param string $amount
     * @return ExpenseLine
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ExpenseLine
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    // }}}

    // if setWhen / getWhen are put here, remove them - we prefer getDate/setDate
    // but don't want to have a field just called 'date'
    // (it is about avoiding a reserved name for a field)
}
