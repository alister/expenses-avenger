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

        $this->em->createQuery('DELETE FROM AppBundle:User')->execute();
        $this->em->createQuery('DELETE FROM AppBundle:Expense')->execute();

        $this->expenseRepo = $this->em->getRepository('AppBundle:Expense');
        $this->u = new User;
        $this->u->setUsername('username')
            ->setPlainPassword('password')
            ->setEmail('email@example.org')
            ->setEnabled(true)
            ->setRoles(['role'])
        ;
    }

    public function testClassSanity()
    {
        $e = new Expense;
        $this->assertInstanceOf('AppBundle\Entity\Expense', $e);

        $this->assertInstanceOf('AppBundle\Entity\User', $this->u);
        //#$this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->em);
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

    /**
     * the big one. Take the pre-generated user and add multiple expenses
     * 
     * Then it, with the expenses are saved to the DB and read back, filtered
     * by date.
     */
    public function testGenerateUserAndExpenses()
    {
        $testExp = $this->expensesSource();
        $this->assertCount(3, $testExp);
        $this->u->addExpense($testExp[0]);
        $this->u->addExpense($testExp[1]);
        $this->u->addExpense($testExp[2]);

        // we put in three
        $this->assertCount(3, $this->u->getExpenses());
        $this->assertContains($testExp[2], $this->u->getExpenses());

        $this->em->persist($this->u);
        $this->em->flush();

        // now filtered by date - only 2 of the above 'expN' are in 2015
        $startDate = new DateTime('2015-01-01');
        $endDate = new DateTime();
        $filtered = $this->expenseRepo->fetchByDate($this->u, $startDate, $endDate);
        $this->assertCount(2, $filtered);

        // filter down to a specific week
        $startDate = new DateTime('2015-04-20'); // Mon 13th Apr to...
        $endDate = new DateTime('2015-04-26'); //   Sun 19th Apr inclusive
        $filtered = $this->expenseRepo->fetchByDate($this->u, $startDate, $endDate);
        $this->assertCount(2, $filtered);
    }
}
