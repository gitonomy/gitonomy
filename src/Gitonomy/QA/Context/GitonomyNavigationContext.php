<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Exception\PendingException;

use WebDriver\By;
use WebDriver\Element;

class GitonomyNavigationContext extends BaseBrowserContext
{
    /**
     * @Then /^I should see a register form$/
     */
    public function iShouldSeeARegisterForm()
    {
        $this->getBrowser()->element(By::xpath('//form//h2[contains(text(), "Register")]'));
    }

    /**
     * @Given /^I am connected as "(.*)"$/
     */
    public function iAmConnectedAs($username)
    {
        $ctx = $this->getMainContext()->getSubcontext('browser');

        $ctx->iAmOn('/logout');
        $ctx->iFillWith('Username', $username);
        $ctx->iFillWith('Password', $username);
        $ctx->iClickOn('Login');
    }

    /**
     * @Given /^I logout$/
     */
    public function iLogout()
    {
        $this->getMainContext()->getSubcontext('browser')->iAmOn('/logout');
    }

    /**
     * @Then /^I click on button with tooltip "(.*)"$/
     */
    public function iClickOnButtonWithTooltip($text)
    {
        $this->getBrowser()->element(By::xpath('//a[contains(@title, "'.$text.'") or contains(@data-original-title, "'.$text.'")]'))->click();
    }


    /**
     * @Then /^I should see an action "([^"]*)" in contextual navigation$/
     */
    public function iShouldSeeAnActionInContextualNavigation($action)
    {
        $elements = $this->getContextualActions($action);

        if (count($elements) != 1) {
            throw new \RuntimeException(sprintf('Expected one action named "%s", found %s actions in contextuel navigation: %s', $action, count($elements), $this->elementsToText($elements)));
        }
    }

    /**
     * @Then /^I should not see an action "([^"]*)" in contextual navigation$/
     */
    public function iShouldNotSeeAnActionInContextualNavigation($action)
    {
        $elements = $this->getContextualActions($action);

        if (count($elements) != 0) {
            throw new \RuntimeException(sprintf('Expected no actions named "%s", found %s actions in contextuel navigation: %s', $action, count($elements), $this->elementsToText($elements)));
        }
    }

    /**
     * @When /^I click on "([^"]*)" in contextual navigation$/
     */
    public function iClickOnInContextualNavigation($action)
    {
        $elements = $this->getContextualActions($action);

        if (count($elements) != 1) {
            throw new \RuntimeException(sprintf('Expected one action named "%s", found %s actions in contextuel navigation: %s', $action, count($elements), $this->elementsToText($elements)));
        }

        $elements[0]->click();
    }


    protected function getContextualActions($textFilter = null)
    {
        return $this->getBrowser()->elements(By::xpath('//div[contains(@class, "sub-actions")]//a[contains(.,"'.$textFilter.'")]'));
    }

    protected function elementsToText($elements)
    {
        return implode(', ', array_map(function (Element $element) {
            return $element->text();
        }, $elements));
    }
}
