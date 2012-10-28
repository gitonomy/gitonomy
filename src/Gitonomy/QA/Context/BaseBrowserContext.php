<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;

use WebDriver\Browser;

class BaseBrowserContext extends BehatContext implements BrowserContextInterface
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
}
