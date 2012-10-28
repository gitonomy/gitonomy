<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

class ProjectController extends Controller
{
    public function listAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_REMEMBERED');

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
        $this->assertGranted('ROLE_PROJECT_CREATE');

        $project = new Project();
        $form    = $this->createForm('project', $project);
        $request = $this->getRequest();

        if ('GET' === $request->getMethod()) {
            return $this->render('GitonomyWebsiteBundle:Project:create.html.twig', array(
                'form' => $form->createView()
            ));
        }

        $form->bind($request);

        if ($form->isValid()) {
            $this->persistEntity($project);
            $this->setFlash('success', $this->trans('notice.success', array(), 'project_create'));

            return $this->redirect($this->generateUrl('project_newsfeed', array('slug' => $project->getSlug())));
        }

        $this->setFlash('error', $this->trans('error.form_invalid', array(), 'register'));

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
