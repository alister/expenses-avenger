<?php
namespace Traits;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Behat\Symfony2Extension\Context\KernelDictionary;

trait Database
{
    /**
     * @BeforeScenario
     */
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $driver = $entityManager->getConnection()->getDriver();
        $supportsForeignKeyConstraints = $driver->getDatabasePlatform()->supportsForeignKeyConstraints();
        if ($supportsForeignKeyConstraints) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 0;");
        }

        $purger = new ORMPurger($entityManager);
        $purger->purge();

        if ($driver->getDatabasePlatform()->supportsIdentityColumns()) {
            $entityManager->getConnection()->executeUpdate("ALTER TABLE expenses AUTO_INCREMENT = 1;");
        }            
        if ($supportsForeignKeyConstraints) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
        }
        $entityManager->clear();
    }

    /**
     * @Given There are the following users
     */
    public function thereAreTheFollowingUsers(TableNode $table)
    {
        #var_dump($table->getHash());echo "\n",__METHOD__,':',__LINE__,"\n";die;
        foreach ($table->getHash() as $hash) {
            $this->createUser($hash);
        }
    }

    public function createUser(array $hash)
    {
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $user->setUsername($hash['username']);
        $user->setPlainPassword($hash['password']);
        $user->setEmail($hash['email']);
        $user->setEmailCanonical(strtolower($hash['email']));
        $user->setEnabled(true);
        $user->setRoles([$hash['role']]);
        $userManager->updateUser($user);
        return $user;
    }

    /**
     * @Given print all users
     */
    public function printAllUsers()
    {
        $x = $this->em->getRepository('AppBundle:User')->findAll();
        dump($x);
    }

}
