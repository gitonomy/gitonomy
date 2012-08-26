<?php

namespace Gitonomy\Bundle\DistributionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigurationController extends Controller
{
    public function welcomeAction()
    {
        return $this->render('GitonomyDistributionBundle:Configuration:welcome.html.twig');
    }
}
