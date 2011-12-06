<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

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
    public function homepageAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->forward('GitonomyFrontendBundle:Main:dashboard');
        }

        return $this->render('GitonomyFrontendBundle:Main:homepage.html.twig');
    }

    /**
     * Dashboard for a connected user.
     */
    public function dashboardAction()
    {
        $this->assertPermission('ROLE_USER');

        return $this->render('GitonomyFrontendBundle:Main:dashboard.html.twig');
    }
}
