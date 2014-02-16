<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Exception\PendingException;
use WebDriver\Behat\AbstractWebDriverContext;
use WebDriver\By;
use WebDriver\Element;
use WebDriver\Util\Xpath;

class GitonomyNavigationContext extends AbstractWebDriverContext
{
    /**
     * @Then /^I should see a register form$/
     */
    public function iShouldSeeARegisterForm()
    {
        $this->getElement(By::xpath('//form//h2[contains(text(), "Register")]'));
    }

    /**
     * @Given /^I am connected as "((?:[^"]|"")*)"(?: with password "((?:[^"]|"")*)")?$/
     */
    public function iAmConnectedAs($username, $password = '')
    {
        $username = $this->unescape($username);
        $password = $this->unescape($password);

        $password = $password ?: $username;

        return array(
            new When('I am on "/logout"'),
            new When('I fill "Username" with "'.$username.'"'),
            new When('I fill "Password" with "'.$password.'"'),
            new When('I click on "Login"'),
        );
    }

    /**
     * @Given /^I logout$/
     */
    public function iLogout()
    {
        return array(
            new When('I am on "/logout"'),
        );
    }

    /**
     * @Then /^I click on button with tooltip "(.*)"$/
     */
    public function iClickOnButtonWithTooltip($text)
    {
        return array(
            new When('I click on "xpath=//a[contains(@title,'.$this->escape(Xpath::quote($text)).') or contains(@data-original-title, '.$this->escape(Xpath::quote($text)).')]"')
        );
    }

    /**
     * @Then /^I should (not )?see a button with tooltip "(.*)"$/
     */
    public function iShouldSeeButtonWithTooltip($verb, $text)
    {
        $expected = $verb === 'not ' ? 0 : 1;

        return array(
            new When('I should see '.$expected.' "xpath=//a[contains(@title, '.$this->escape(Xpath::quote($text)).') or contains(@data-original-title, '.$this->escape(Xpath::quote($text)).')]"'),
        );
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
        return $this->getElements(By::xpath('//div[contains(@class, "sub-actions")]//a[contains(.,"'.$textFilter.'")]'));
    }

    protected function elementsToText($elements)
    {
        return implode(', ', array_map(function (Element $element) {
            return $element->text();
        }, $elements));
    }
}
