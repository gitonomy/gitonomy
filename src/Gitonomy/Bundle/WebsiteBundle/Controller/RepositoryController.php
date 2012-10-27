<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RepositoryController extends Controller
{
    public function overviewAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Repository:overview.html.twig');
    }
}
