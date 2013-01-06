<?php

namespace Gitonomy\QA\Initializer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Behat\Behat\Context\Initializer\InitializerInterface;
use Behat\Behat\Context\ContextInterface;

use Gitonomy\QA\Context\BrowserContextInterface;

use WebDriver\Client as WebDriverClient;
use WebDriver\Capabilities;

class BrowserAwareInitializer implements InitializerInterface, EventSubscriberInterface
{
    private $client;
    private $capabilities;
    private $baseUrl;
    private $timeout;

    private $browser;

    public function __construct(WebDriverClient $client, Capabilities $capabilities, $baseUrl, $timeout)
    {
        $this->client       = $client;
        $this->capabilities = $capabilities;
        $this->baseUrl      = $baseUrl;
        $this->timeout      = $timeout;
    }

    public function supports(ContextInterface $context)
    {
        return $context instanceof BrowserContextInterface;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        if (null === $this->browser) {
            $this->browser = $this->client->createBrowser($this->capabilities);
            $this->browser->setImplicitTimeout($this->timeout);
        }

        $context->setBrowser($this->browser, $this->baseUrl);
    }

    public function close()
    {
        if ($this->browser) {
            $this->browser->close();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'afterSuite' => 'close'
        );
    }
}
