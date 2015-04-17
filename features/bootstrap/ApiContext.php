<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\HttpFoundation\Session\Session;

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
}
