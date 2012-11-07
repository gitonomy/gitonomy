<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Exception\PendingException;

use WebDriver\By;

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
}
