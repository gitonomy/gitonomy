<?php

namespace Gitonomy\Bundle\DistributionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigurationController extends Controller
{
    public function welcomeAction()
    {
        $steps = $this->get('gitonomy_distribution.steps');

        return $this->render('GitonomyDistributionBundle:Configuration:welcome.html.twig', array(
            'steps' => $steps
        ));
    }
}
