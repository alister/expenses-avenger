<?php
namespace Traits;

trait JsonSites
{
    /**
     * Go to a specific route
     *
     * @When I call the JSON route :routeName
     * @When I call the json route :routeName with agent :agent
     */
    public function iCallTheJsonApiRoute($routeName, $agent = null)
    {
        $url = $this->getService('router')->generate($routeName, []);
        return $this->iCallTheJsonApiUrl($url, $agent);
    }

    /**
     * Go to a specific URL, the baseUrl is prepended
     *
     * @When I call the json API :url
     * @When I call the json API :url with agent :agent
     */
    public function iCallTheJsonApiUrl($url, $agent = null)
    {
        $url = $this->baseUrl . $url;
        //#var_dump($url);echo "\n",__METHOD__,':',__LINE__,"\n";die;

        if ($agent) {
            $this->session->setRequestHeader('user-agent', $agent);
        }

        $this->session->visit($url);

        $this->response = $this->session->getPage()->getContent();
    }

    /**
     * @Then the response should be JSON
     * @Then the response should be JSON with status :status
     */
    public function theResponseShouldBeJson($status = 200)
    {
        $data = json_decode($this->response);
        if (empty($data)) {
            throw new \Exception("Response was not JSON\n" . $this->response);
        }
        $this->assert->statusCodeEquals($status);
    }
}
