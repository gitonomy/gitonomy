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

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Controller extends BaseController
{
    protected function mail($to, $template, $context)
    {
        $this->get('gitonomy.mailer')->mail($to, $template, $context);
    }

    protected function setFlash($name, $value)
    {
        $this->get('session')->getFlashBag()->set($name, $value);
    }

    protected function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    protected function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->get('translator')->transChoice($id, $number, $parameters, $domain, $locale);
    }

    protected function isAuthenticated()
    {
        return $this->get('security.context')->getToken()->getUser() instanceof User;
    }

    protected function assertGranted($attributes, $object = null)
    {
        if (!$this->isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->get('security.context')->isGranted($attributes, $object);
    }

    protected function getRepository($name)
    {
        return $this->get('doctrine')->getManager()->getRepository($name);
    }

    protected function persistEntity($entity)
    {
        $em = $this->get('doctrine')->getManager();
        $em->persist($entity);
        $em->flush();
    }

    protected function flush()
    {
        $em = $this->get('doctrine')->getManager();
        $em->flush();
    }

    protected function removeEntity($entity)
    {
        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();
    }

    protected function createToken($intention)
    {
        return $this->get('form.csrf_provider')->generateCsrfToken($intention);
    }

    protected function isTokenValid($intention, $token)
    {
        return $this->get('form.csrf_provider')->isCsrfTokenValid($intention, $token);
    }

    protected function dispatch($eventName, Event $event)
    {
        $this->get('gitonomy_core.event_dispatcher')->dispatch($eventName, $event);
    }

    protected function json($data)
    {
        return new JsonResponse($data);
    }
}
