<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function listAction()
    {
        return $this->render('GitonomyWebsiteBundle:Project:list.html.twig');
    }

    public function createAction()
    {
        $form = $this->get('form.factory')->createNamedBuilder('project')
            ->add('slug', 'text')
            ->add('name', 'text')
            ->getForm()
        ;

        return $this->render('GitonomyWebsiteBundle:Project:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newsfeedAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Project:newsfeed.html.twig');
    }

    public function historyAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Project:history.html.twig');
    }

    public function sourceAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Project:source.html.twig');
    }

    public function branchesAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Project:branches.html.twig');
    }

    public function tagsAction($slug)
    {
        return $this->render('GitonomyWebsiteBundle:Project:tags.html.twig');
    }
}
