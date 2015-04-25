<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\ExpenseRepository;
use AppBundle\ExpenseManager;
use Mockery as M;
#use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExpenseManagerTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();

        #$this->eRepo = M::Mock('AppBundle\Entity\ExpenseRepository');
        #$this->eRepo = new ExpenseRepository;
        $this->e = $this->container->get('app.expense_manager');
    }

    /**
     * @dataProvider filterQueryProvider
     */
    public function testBuildFilterQuery($filterParams, $expected)
    {
        $actual = $this->e->buildFilterQuery($filterParams);
        //$this->assertNotEmpty($actual, "expected buildFilterQuery to do something");

        // we test against the expected DQL
        $this->assertEquals($expected, $actual->getDql());
    }

    public function filterQueryProvider()
    {
        return array(
            [ [], "SELECT e FROM AppBundle\Entity\Expense e", ],
            [   [ 'startDate' => '2015-01-01',
                  'endDate'   => '2015-02-31',
                ],
                 "SELECT e FROM AppBundle\Entity\Expense e WHERE (e.createdAt >= :startDate) AND (e.createdAt <= :endDate)",
             ],
        );
    }
}
