<?php

use Alex\MailCatcher\Behat\MailCatcherContext;
use Behat\Behat\Context\BehatContext;
use Gitonomy\QA\Context\ApiContext;
use Gitonomy\QA\Context\GitonomyNavigationContext;
use WebDriver\Behat\WebDriverContext;

class FeatureContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->useContext('api', new ApiContext());
        $this->useContext('gitonomy_navigation', new GitonomyNavigationContext());
        $this->useContext('webdriver', new WebDriverContext());
        $this->useContext('mailcatcher', new MailCatcherContext());
    }
}
