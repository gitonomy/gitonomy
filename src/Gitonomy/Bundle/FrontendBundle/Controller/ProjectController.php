<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

class ProjectController extends BaseController
{
    public function showAction($slug)
    {
        $project = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Project')->findOneBy(array('slug' => $slug));

        if(null === $project) {
            throw $this->createNotFoundException(sprintf('Project with slug %s does not exists', $slug));
        }

        return $this->render('GitonomyFrontendBundle:Project:show.html.twig', array(
            'project' => $project
        ));
    }
}
