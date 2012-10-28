<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

class ProjectController extends Controller
{
    public function listAction()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $pool = $this->get('gitonomy_core.git.repository_pool');

        $entities = $this->getRepository('GitonomyCoreBundle:Project')->findByUser($this->getUser());
        $projects = array();
        foreach ($entities as $entity) {
            $projects[] = array($entity, $pool->getGitRepository($entity));
        }

        return $this->render('GitonomyWebsiteBundle:Project:list.html.twig', array(
            'projects' => $projects
        ));
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
