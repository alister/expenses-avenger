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

    /** @var string */
    protected $u;

    protected function setUp()
    {
        $client = static::createClient();
        $this->container = $client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        #$this->em->createQuery('DELETE FROM AppBundle:User')->execute();
        $this->em->createQuery('DELETE FROM AppBundle:Expense')->execute();

        $this->expenseRepo = $this->em->getRepository('AppBundle:Expense');
        $this->u = "test-user";     // also in AppBundle\UserAppForTesting
    }

    public function testClassSanity()
    {
        $e = new Expense;
        $this->assertInstanceOf('AppBundle\Entity\Expense', $e);
    }

    public function createExpense(DateTime $date, $amount, $desc, $comment)
    {
        $expense = new Expense();
        $expense
            ->setUser($this->u)
            ->setCreatedAt($date)
            ->setDescription($desc)
            ->setAmount($amount)
            ->setComment($comment);
        return $expense;
    }

    public function testCreateExpense()
    {
        $date = new DateTime('2015-04-20T11:18:01+0100');
        $exp = $this->createExpense(
            $date,
            '5.98',
            'breakfast',
            'yep, I went out for a cooked breakfast'
        );
        $this->assertInstanceOf('AppBundle\Entity\Expense', $exp);

        $this->assertEquals("5.98", $exp->getAmount());
        $this->assertEquals($date, $exp->getCreatedAt());
    }

    private function expensesSource()
    {
        // create 3 rows, one in 2014, 2 in 2015-01
        $exp1 = $this->createExpense(
            new DateTime('2014-12-31 23:50:01+0100'),
            '65.00', 
            'dinner', 
            'Late new year dinner'
        );
        $exp2 = $this->createExpense(
            new DateTime('2015-04-20 11:18:01+0100'),
             '5.98', 
             'breakfast', 
             'a cooked breakfast'
         );
        $exp3 = $this->createExpense(
            new DateTime('2015-04-21 11:18:01+0100'),
             '5.98', 
             'breakfast', 
             'out for a cooked breakfast'
         );
        return [$exp1, $exp2, $exp3, ];
    }
}
