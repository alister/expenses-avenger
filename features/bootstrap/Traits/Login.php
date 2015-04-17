<?php
namespace Traits;

use Behat\Mink\WebAssert;
#require_once 'PHPUnit/Framework/Assert/Functions.php';

trait Login
{
    /**
     * @Given I have successfully logged in as :username
     * @Given I have successfully logged in as :username with :password
     *
     * If only a username is given, password defaults to 'password'
     */
    public function iHaveSuccessfullyLoggedInAs($username, $password = 'password')
    {
        $this->visit("/login");
        $this->assertPageAddress("/login");

        $this->submitLoginForm($username, $password);
        $this->assertPageContainsText("Signed in as {$username}");
    }

    /**
     * @When I log in as :username with :password
     * @When I log in as :username
     *
     * @param string $username [description]
     * @param string $password [description]
     *
     * @return void
     */
    public function submitLoginForm($username = '-', $password = 'password')
    {
        $this->fillField('username', $username);
        $this->fillField('password', $password);
        $this->pressButton('_submit');
        $this->assertResponseNotContains('Invalid username or password');
        $this->assertResponseNotContains('bad credentials');
    }
}
