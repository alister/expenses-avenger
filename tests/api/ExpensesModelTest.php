<?php
namespace AppBundle\Tests\Api;

use DateTime;
use Mockery as M;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use AppBundle\Entity\Expense;

class ExpensesModelTest extends WebTestCase
{
    /** @var Expense */
    protected $e;

    /** @var Users */
    protected $u;

    protected function setUp()
    {
        $client = static::createClient();
        $this->container = $client->getContainer();

        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->u = new User;
        $this->expenseRepo = $this->em->getRepository('AppBundle:Expense');
    }

    public function testClassSanity()
    {
        $e = new Expense;
        $this->assertInstanceOf('AppBundle\Entity\Expense', $e);

        $this->assertInstanceOf('AppBundle\Entity\User', $this->u);
        //#$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->em);
    }

    public function createExpense(DateTime $when, $amount, $desc, $comment)
    {
        $expense = new Expense();
        $expense
            ->setUser($this->u)
            ->setDate($when)
            ->setDescription($desc)
            ->setAmount($amount)
            ->setComment($comment);
        return $expense;
    }

    public function testCreateExpense()
    {
        $when = new DateTime('2015-04-20T11:18:01+0100');
        $exp = $this->createExpense(
            $when,
            '5.98',
            'breakfast',
            'yep, I went out for a cooked breakfast'
        );
        $this->assertInstanceOf('AppBundle\Entity\Expense', $exp);

        $this->assertEquals("5.98", $exp->getAmount());
        $this->assertEquals($when, $exp->getDate());
    }

    public function testReturnSomeExpenseLines()
    {
        $startDate = new DateTime('2015-01-01');
        $endDate = new DateTime();

        $prevDate = new DateTime('2014-12-31T23:50:01+0100');
        $whenDate = new DateTime('2015-04-20T11:18:01+0100');
        // create 3 rows, one in 2014, 2 in 2015-01
        $exp1 = $this->createExpense($prevDate, '5.98', 'dinner', 'Late new year dinner');
        $exp2 = $this->createExpense($whenDate, '5.98', 'breakfast', 'out for a cooked breakfast');
        $exp3 = $this->createExpense($whenDate, '5.98', 'breakfast', 'out for a cooked breakfast');
        $this->u->addExpense($exp1);
        $this->u->addExpense($exp2);
        $this->u->addExpense($exp3);

        // we put in three
        $this->assertCount(3, $this->u->getExpenses());

        // now filtered by date
        #$filtered = $this->expenseRepo->fetchByDateDesc($startDate, $endDate);
        #$this->assertCount(2, $filtered);
    }
}
