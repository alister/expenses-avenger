<?php
namespace Traits;

trait Api
{
    /**
     * Go to a specific route
     *
     * @When I call the API route :routeName
     * @When I call the API route :routeName format :format
     * @When I call the API route :routeName with agent :agent format :format
     */
    public function iCallTheApiRoute($routeName, $format = '', $agent = null)
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
            $data = json_decode($this->response);
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
