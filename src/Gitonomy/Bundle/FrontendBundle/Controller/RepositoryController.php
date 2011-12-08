<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Controller for repository actions.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class RepositoryController extends BaseController
{
    public function blockCommitHistoryAction($id)
    {
        return $this->render('GitonomyFrontendBundle:Repository:blockCommitHistory.html.twig', array(
        ));
    }
}
