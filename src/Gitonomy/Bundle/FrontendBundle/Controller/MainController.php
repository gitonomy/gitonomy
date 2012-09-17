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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Main pages of the application.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MainController extends BaseController
{
    /**
     * Root of the website, displaying informations or redirecting to the
     * dashboard.
     */
    public function homepageAction($_locale = null)
    {
        $this->get('doctrine')->getManager()->getRepository('GitonomyCoreBundle:User')->findOneByUsername('alice');

        if (null === $_locale) {
            return new RedirectResponse($this->generateUrl('gitonomyfrontend_main_homepage'));
        }

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->forward('GitonomyFrontendBundle:Main:dashboard');
        }

        return $this->render('GitonomyFrontendBundle:Main:homepage.html.twig');
    }

    /**
     * Dashboard for a connected user.
     */
    public function dashboardAction()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException();
        }

        $pool = $this->get('gitonomy_core.git.repository_pool');

        $entities = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Project')->findByUser($this->getUser());
        $projects = array();
        foreach ($entities as $entity) {
            $projects[] = array($entity, $pool->getGitRepository($entity));
        }

        return $this->render('GitonomyFrontendBundle:Main:dashboard.html.twig', array(
            'projects' => $projects
        ));
    }

    /**
     * Action for changing the current locale.
     */
    public function setLocaleAction($locale)
    {
        $referer = $this->getRequest()->headers->get('Referer');
        if ($referer) {
            $redirect = str_replace('/'.$this->getRequest()->getLocale(), '/'.$locale, $referer);
        } else {
            $redirect = $this->generateUrl('gitonomyfrontend_main_homepage', array('_locale' => $locale));
        }

        return new RedirectResponse($redirect);
    }
}
