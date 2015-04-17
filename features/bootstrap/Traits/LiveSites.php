<?php
namespace Traits;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\WebAssert;

/**
 * Trait to enable
 *
 * Designed to be used in a Contact class that:
 *   class ExampleContext "extends RawMinkContext implements Context"
 *
 * Requires a Behat.yml setup with a 'baseUrl' fully qualified URL, unless
 *   live_smoketest:
 *      contexts:
 *          - LiveContext:
 *              baseUrl: http://www.contractavailability.com
 *      paths:
 *          - %paths.base%/features/smoke
*/
trait LiveSites
{
    /**
     * [$baseUrl description]
     * @var string
     */
    private $baseUrl;

    /**
     * @var [type]
     */
    private $session;

    /** @var Behat\Mink\WebAssert */
    private $assert;

    public function __construct($baseUrl = '')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @BeforeScenario
     *
     * Because we went to visit specific URLs, with domain names, we have to
     * set it up ourselves
     *
     * @return void
     */
    public function beforeScenario()
    {
        $this->session = $this->getSession('goutte');
        $this->session->start();
        $this->assert = new WebAssert($this->session);

        // because we are changing the referrer and agent in the test, we have to
        // make sure they are set to something ordinary otherwise
        $this->session->setRequestHeader('referer', '');
        $client = $this->session->getDriver()->getClient()->getClient();
        $this->session->setRequestHeader('user-agent', $client->getDefaultUserAgent());
    }

    /**
     * @When I go to the URL :url
     * @When I go to the URL :url with an agent string :agent
     *
     * If baseUrl was defined in the constructor (via the behat config parameter)
     * then the URL here can be relative. If not, it must be a
     * full URL http://example.com/path...
     */
    public function iGoToTheUrl($url, $agent = null)
    {
        $urlFinal = $this->baseUrl . $url;
        // $urlFinal should have a complete protocol//hostname/(optional path)

        if ($agent) {
            $this->session->setRequestHeader('user-agent', $agent);
        }

        $this->session->visit($urlFinal);
    }

    /**
     * @When I go to the URL :url with a referrer :refString
     */
    public function iGoToTheUrlWithReferrer($url, $refString)
    {
        $urlFinal = $this->baseUrl . $url;
        // $urlFinal should have a complete protocol//hostname/(optional path)

        if (!$refString) {
            throw new \Exception("refString not given");
        }

        $this->session->setRequestHeader('referer', $refString);

        $this->session->visit($urlFinal);
    }

    /**
     * @Then the url should match :expectedDomainWithPath
     *
     * match the entire URL with what we ended up at
     */
    public function theFinalUrlShouldMatch($expectedDomainWithPath)
    {
        $actual = $this->session->getCurrentUrl();
        assertThat($expectedDomainWithPath, equalTo($actual));
            #"expected '{$expectedDomainWithPath}' - got '{$actual}'"
        // assertEquals(
        //     $expectedDomainWithPath,
        //     $actual,
        //     "expected '{$expectedDomainWithPath}' - got '{$actual}'"
        // );
    }

    /**
     * @Then the host should match :expectedHostname
     *
     * Just match the domain name, no protocol or path
     */
    public function theHostShouldMatch($expectedHostname)
    {
        $actualHostName = parse_url($this->session->getCurrentUrl(), PHP_URL_HOST);
        assertThat($expectedHostname, equalTo($actualHostName));
        // assertEquals(
        //     $expectedHostname,
        //     $actualHostName,
        //     "expected '{$expectedHostname}' - got '{$actualHostName}'"
        // );
    }

    /**
     * @Then the status should be :code
     */
    public function theResponseCodeShouldBe($code)
    {
       $this->assert->statusCodeEquals($code);
    }

    /**
     * @Then the response should contain :content
     *
     * Somewhere on the page, or in a comment
     */
    public function theResponseShouldContain($content)
    {
        $this->assert->responseContains($content);
    }

    /**
     * @Then I should see the text :text
     * @Then I should see :text
     *
     * Visible text, not in a comment
     */
    public function iShouldSeeTheText($text)
    {
        $this->assert->pageTextContains($text);
    }

    /**
     * Checks, that page doesn't contain specified text.
     *
     * @Then I should not see :text
     */
    public function assertPageNotContainsText($text)
    {
        $this->assert->pageTextNotContains($text);
    }

    /**
     * @Then print current URL
     */
    public function printCurrentUrl()
    {
        echo $this->session->getCurrentUrl();
    }
}
