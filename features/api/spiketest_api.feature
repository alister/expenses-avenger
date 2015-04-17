Feature: The most basic API features work
    In order to be confident the API is installed
    As a developer
    I need to check the API returns the test data

    Scenario Outline: The test API returns sensibly
        When I call the JSON route "api_spiketest"
        Then the response should be JSON
         And the SpikeApi application name should be <sitename>
         #And the SpikeApi Request Time should be very recent
         #And the SpikeApi Load Average should be OK
    Examples:
        | sitename  |
        | expenses  |
        #| fred     | # prove it fails on command
