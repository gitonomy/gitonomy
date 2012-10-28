<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Exception\PendingException;

use WebDriver\By;

class GitonomyNavigationContext extends BaseBrowserContext
{
    /**
     * @Given /^I am on a page with a menu$/
     */
    public function iAmOnAPageWithAMenu()
    {
        throw new PendingException();
    }

    /**
     * @When /^I open menu "(.*)"$/
     */
    public function iOpenMenu($text)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see menu "(.*)"$/
     */
    public function iShouldSeeMenu($text)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see (\d+) buttons to delete SSH keys$/
     */
    public function iShouldSeeButtonsToDeleteSshKeys($count)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see navigation menu with my fullname "(.*)"$/
     */
    public function iShouldSeeNavigationMenuWithMyFullname($fullname)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see a register form$/
     */
    public function iShouldSeeARegisterForm()
    {
        $this->getBrowser()->element(By::xpath('//form//h2[contains(text(), "Register")]'));
    }

    /**
     * @Given /^I should see the installation breadcrumb$/
     */
    public function iShouldSeeTheInstallationBreadcrumb()
    {
        throw new PendingException();
    }
}
