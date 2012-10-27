<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdministrationController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('GitonomyWebsiteBundle:Administration:dashboard.html.twig');
    }

    public function usersAction()
    {
        return $this->render('GitonomyWebsiteBundle:Administration:users.html.twig');
    }

    public function rolesAction()
    {
        return $this->render('GitonomyWebsiteBundle:Administration:roles.html.twig');
    }
}
