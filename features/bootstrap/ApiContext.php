<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Expense;
use AppBundle\Entity\User;

/**
 * Using tests (traits) for getting and checking websites from full URLs and
 * JSON api calls, check websites
 *
 * This is used as a common context for cross-domain checks
 */
class ApiContext extends RawMinkContext implements Context, KernelAwareContext, SnippetAcceptingContext
{
    use KernelDictionary;
    use Traits\LiveSites;
    use Traits\Api;
    use Traits\SpikeApi;
    use Traits\Database;

    /**
     * Initializes context. Every scenario gets its own context object.
     *
     * @param ContractorRepository $contractor_repo [description]
     * @param UserManager          $user_manager    [description]
     */
    public function __construct(
        $baseUrl = '',
        EntityManager $entity_manager
    ) {
        $this->em          = $entity_manager;
        $this->baseUrl     = $baseUrl;
    }

    /**
     * @Then the response should be OK, record::recNo
     */
    public function theResponseShouldBeOkWithRecord($recNo)
    {
        throw new PendingException();
    }

    /**
     * Get service by id.
     *
     * Requires KernelDictionary & KernelAwareContext
     *
     * @param string $id
     *
     * @return object service from kernel
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    private function convertExpense($key, $val)
    {
        switch ($key) {
        case 'createdAt':
            $x = DateTime::createFromFormat(DateTime::W3C, $val);
            return $x;
        default:
            //#dump($key, $val);
            return $val;
            break;
        }
    }

    /**
     * @Given These expense lines exist
     */
    public function TheseExpensesExist(TableNode $table)
    {
        // get any fully persisted user
        $user = $this->em->getRepository('AppBundle:User')->findOneBy([]);

        foreach ($table->getHash() as $hash) {
            $line = new Expense();
            foreach ($hash as $key => $val) {
                $setter = 'set'.ucfirst($key);

                $val = $this->convertExpense($key, $val);
                $line->$setter($val);
            }

            $line->setUser($user);
            $this->em->persist($line);
            $this->expenses[] = $line;
        }
        $this->em->flush();
    }

    /**
     * @When I call the API route :route for record :id
     */
    public function iCallTheApiRouteForRecord($route, $id)
    {
        $url = $this->getService('router')->generate($route, ['_format'=>'json', 'id'=> $id]);
        return $this->iCallTheApiUrl($url);
    }

    /**
     * @Then the response should have a field :fieldname
     */
    public function theResponseShouldHaveAField($fieldname)
    {
        $fieldname = $this->mapFieldNameToProperty($fieldname);
        $line = json_decode($this->response);
        assertThat($line, set($fieldname));
    }

    private function mapFieldNameToProperty($fieldname)
    {
        $map = array(
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        );
        if (isset($map[$fieldname])) {
            return $map[$fieldname];
        }
        return $fieldname;
    }

    /**
     * @When I see all the expenses
     * 
     * dump everything in the database
     */
    public function iSeeAllTheExpenses()
    {
        $lines = $this->em->getRepository('AppBundle:Expense')->findAll();
    }

    /**
     * @When I see the API response
     * 
     * dump everything in the response
     */
    public function printResponse()
    {
        dump($this->response);
    }

    /**
     * @When die
     */
    public function andDie()
    {
        die;
    }
}
