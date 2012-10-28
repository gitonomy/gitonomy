<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Gitonomy\QA\Context\BrowserContext;
use Gitonomy\QA\Context\ApiContext;
use Gitonomy\QA\Context\GitonomyNavigationContext;

class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('browser', new BrowserContext());
        $this->useContext('api', new ApiContext());
        $this->useContext('gitonomy_navigation', new GitonomyNavigationContext());
    }

    /**
     * @Given /^I should receive a mail with subject "([^"]*)"$/
     */
    public function iShouldReceiveAMailWithSubject($arg1)
    {
        throw new PendingException();
    }
}
