<?php
namespace Traits;

trait SpikeApi
{
    // {{{ testing the return from the expenses/api/spiketest.json API

    /**
     * @Then the SpikeApi application name should be :name
     */
    public function theSpikeTestApiApplicationNameShouldBeExpenses($name)
    {
        $data = json_decode($this->response);
        $actual = $data->appname;

        assertThat($name, $actual);
    }

    /**
     * @Then the SpikeApi Request Time should be very recent
     */
    public function theRequestTimeShouldBeVeryRecent()
    {
        $now = time();
        $data = json_decode($this->response);
        $actual = $data->request_time;

        assertThat($actual, typeOf('integer'));
        #assertInternalType('integer', $actual);
        // we need to allow some fuzz-time, a few seconds should be OK
        assertThat("request time should be close", $now, closeTo($actual, 3));
        // We give it a little fuzz to allow for potential clock drift between machines
    }

    /**
     * @Then the SpikeApi Load Average should be OK
     */
    public function theLoadAverageShouldBeOk()
    {
        $data = json_decode($this->response);
        $ldAvg = $data->ldAvg;

        $requiredMax1MinLdAvg = 1.25;
        $requiredMax5MinLdAvg = 0.75;
        $requiredMax15MinLdAvg = 0.45;

        assertThat('1 Min LdAvg is high', $ldAvg[0],  lessThanOrEqualTo($requiredMax1MinLdAvg));
        assertThat('5 Min LdAvg is high', $ldAvg[1],  lessThanOrEqualTo($requiredMax5MinLdAvg));
        assertThat('15 Min LdAvg is high', $ldAvg[2], lessThanOrEqualTo($requiredMax15MinLdAvg));
    }

    // }}}
}
