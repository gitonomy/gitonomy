<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;

use WebDriver\Browser;

class BrowserContext extends BehatContext
{
    protected $browser;
    protected $baseUrl;

    public function setBrowser(Browser $browser, $baseUrl)
    {
        $this->browser = $browser;
        $this->baseUrl = preg_match('#/^$#', $baseUrl) ? $baseUrl : $baseUrl.'/';
    }

    public function getBrowser()
    {
        if (null === $this->browser) {
            throw new \RuntimeException('Browser is missing');
        }

        return $this->browser;
    }

    /**
     * @Given /^I am on "(.+)"$/
     */
    public function iAmOn($url)
    {
        if (!preg_match('#^https?://#', $url)) {
            $url = $this->baseUrl . (substr($url, 0, 1) == "/" ? substr($url, 1) : $url);
        }

        $this->getBrowser()->open($url);
    }

    /**
     * @Then /^I should see a title "(.*)"$/
     */
    public function iShouldSeeATitle($text)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click on "(.*)"$/
     */
    public function iClickOn($text)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see "(.*)"$/
     */
    public function iShouldSee($text)
    {
        throw new PendingException();
    }

    /**
     * @When /^I fill:$/
     */
    public function iFill(TableNode $table)
    {
        throw new PendingException();
    }
}
