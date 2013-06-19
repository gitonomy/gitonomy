<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Component\Config\ConfigInterface;
use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ConfigListener implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', -256)),
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        // locale
        $token = $this->container->get('security.context')->getToken();
        if (null === $token || ! $token->getUser() instanceof User || $token->getUser()->getLocale() === null) {
            $locale = $this->container->get('gitonomy_core.config')->get('locale');
        } else {
            $locale = $token->getUser()->getLocale();
        }

        if (null === $locale) {
            throw new \RuntimeException('No locale found in configuration');
        }

        $this->container->get('translator')->setLocale($locale);
        \Locale::setDefault($locale);
    }
}
