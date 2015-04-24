<?php
namespace Traits;

use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

trait Api
{
    /**
     * POST to a specific route
     * 
     * @When I send a POST request to route :route with values:
     */
    public function iSendAPostRequestToRouteWithValues($route, TableNode $table)
    {
        $post = [];

        foreach ($table->getRows() as $hash) {
            $post[$hash[0]] = $hash[1]; 
        }
        $url = $this->getService('router')->generate($route, $post);

        $this->client = $this->getService('session');
        
        $crawler  = $this->jsonRequest($url, $post);
        $response = $this->client->getResponse();

        //#dump($post, $response);echo "\n",__METHOD__,':',__LINE__,"\n";#die;
        throw new PendingException();
    }

    /**
     * copied from Bazinga\Bundle\RestExtraBundle\Test\WebTestCase
     * 
     * We don't need the bundle, but these couple of functions are useful
     */
    protected function jsonRequest($verb, $endpoint, array $data = array())
    {
        $data = empty($data) ? null : json_encode($data);

        return $this->client->request($verb, $endpoint,
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $data
        );
    }

    /**
     * Go to a specific route
     *
     * @When I call the API route :routeName
     * @When I call the API route :routeName format :format
     * @When I call the API route :routeName with agent :agent format :format
     */
    public function iCallTheApiRoute($routeName, $post = [], $format = '', $agent = null)
    {
        $url = $this->getService('router')->generate($routeName, []);
        return $this->iCallTheApiUrl($url, $format, $agent);
    }

    /**
     * Go to a specific URL, the baseUrl is prepended
     *
     * @When I call the API :url format :format
     * @When I call the API :url with agent :agent format :format
     */
    public function iCallTheApiUrl($url, $format = '', $agent = null)
    {
        if ($format) {
            $format = ".$format";
        }
        $url = $this->baseUrl . $url . $format;

        if ($agent) {
            $this->session->setRequestHeader('user-agent', $agent);
        }

        $this->session->visit($url);

        $this->response = $this->session->getPage()->getContent();
    }

    /**
     * @Then the response should be :format
     * @Then the response should be :format with status :status
     */
    public function theResponseShouldBe($format = 'json', $status = 200)
    {
        if ($format == 'json') {
            $data = json_decode($this->response, true);
        } elseif ($format == 'xml') {
            $data = simplexml_load_string($this->response); 
        } else {
            throw new Exception("Unknown format to confirm");
        }

        if (empty($data)) {
            throw new \Exception("Response was bad");
        }
        $this->assert->statusCodeEquals($status);
    }
}
