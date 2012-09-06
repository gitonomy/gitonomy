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

        if ($this->get('security.context')->isGranted('AUTHENTICATED')) {
            return $this->forward('GitonomyFrontendBundle:Main:dashboard');
        }

        return $this->render('GitonomyFrontendBundle:Main:homepage.html.twig');
    }

    /**
     * Dashboard for a connected user.
     */
    public function dashboardAction()
    {
        $this->assertPermission('AUTHENTICATED');

        return $this->render('GitonomyFrontendBundle:Main:dashboard.html.twig');
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
