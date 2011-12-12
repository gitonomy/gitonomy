<?php

namespace Gitonomy\Bundle\FrontendBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerifyLocaleListener
{
    protected $allowedLocales;
    protected $defaultLocale;

    public function __construct($allowedLocales, $defaultLocale)
    {
        $this->allowedLocales = $allowedLocales;
        $this->defaultLocale  = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $e)
    {
        if ($e->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $e->getRequest();
        $locale  = $request->attributes->get('_locale');

        if (null === $locale) {
            return;
        }

        if (true === in_array($locale, $this->allowedLocales, true)) {
            return;
        }

        throw new NotFoundHttpException(sprintf('Locale "%s" not available', $locale));
    }
}