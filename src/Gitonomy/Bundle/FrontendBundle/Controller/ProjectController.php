<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

/**
 * Controller for project displaying.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectController extends BaseController
{
    /**
     * Displays the project main page
     */
    public function showAction($slug)
    {
        $project = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Project')->findOneBy(array('slug' => $slug));

        if(null === $project) {
            throw $this->createNotFoundException(sprintf('Project with slug %s does not exists', $slug));
        }

        $this->assertProjectPermission($project, 'PROJECT_VIEW');

        return $this->render('GitonomyFrontendBundle:Project:show.html.twig', array(
            'project' => $project
        ));
    }
}
