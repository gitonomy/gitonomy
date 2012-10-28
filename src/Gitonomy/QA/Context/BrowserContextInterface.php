<?php

namespace Gitonomy\QA\Context;

use WebDriver\Browser;

interface BrowserContextInterface
{
    public function setBrowser(Browser $browser, $baseUrl);
}
