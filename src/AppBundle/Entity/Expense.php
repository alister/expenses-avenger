<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Expense - one line of expenses for a (connected) user
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ExpenseRepository")
 * @ORM\Table(name="expenses")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("expense")
 * @Serializer\AccessType("public_method")
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
     * @Serializer\Expose
     */
    private $id;

    /**
     * userApp.io username of person that created this
     * 
     * @ORM\Column(name="user", type="string")
     * @var string
     */
    private $user;

    /**
     * Date of expense (input by user, default to today)
     * 
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Serializer\Expose
     * @Serializer\Type("DateTime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Serializer\Expose
     */
    private $description;

    /**
     * @var string
     *
     * a bit of a hack here - SQLite doesn't have a DECIMAL type, so it stores
     * it as a float - which is way too easy to drift with accuracy problems
     * 
     * So, we'll store it here as a string, as coerce it as required when 
     * we come to add things up.
     * 
     * Again, MySQL shouldn't have the problem.
     * 
     * @ORM\Column(name="amount", type="decimal", precision=7, scale=2)
     * @Serializer\Expose
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     * @Serializer\Expose
     */
    private $comment;

    // =================================================================


    /**
     * default the createdAt to {NOW}
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id of this expense (unique amongst all lines)
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Expense
     */
    public function setCreatedAt($createdAt)
    {
        if (! $createdAt instanceof \DateTime) {
            $createdAt = new \DateTime($createdAt);
        }
        //dump($createdAt);
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getcreatedAt()
    {
        return $this->createdAt;
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
     * and then it's co-erced as required. In the (mysql)DB it's a DECIMAL
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

}
