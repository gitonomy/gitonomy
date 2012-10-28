<?php

namespace Gitonomy\QA\Initializer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Behat\Behat\Context\Initializer\InitializerInterface;
use Behat\Behat\Context\ContextInterface;

use Gitonomy\QA\Context\ApiContext;

use Gitonomy\QA\KernelFactory;

class KernelAwareInitializer implements InitializerInterface, EventSubscriberInterface
{
    private $kernelFactory;

    public function __construct(KernelFactory $kernelFactory)
    {
        $this->kernelFactory = $kernelFactory;
    }

    public function supports(ContextInterface $context)
    {
        return $context instanceof ApiContext;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setKernelFactory($this->kernelFactory);
    }

    public static function getSubscribedEvents()
    {
        return array();
    }
}
