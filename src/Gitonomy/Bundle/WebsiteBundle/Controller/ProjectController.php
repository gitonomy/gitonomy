<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Git\Blob;
use Gitonomy\Git\Tree;
use Gitonomy\Git\Reference;
use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Component\Pagination\Adapter\GitLogAdapter;

class ProjectController extends Controller
{
    public function listAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_REMEMBERED');

        $pool = $this->get('gitonomy_core.git.repository_pool');

        if ($this->isGranted('ROLE_ADMIN')) {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findAll();
        } else {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findByUser($this->getUser());
        }

        return $this->render('GitonomyWebsiteBundle:Project:list.html.twig', array(
            'projects' => $projects
        ));
    }

    public function createAction()
    {
        $this->assertGranted('ROLE_PROJECT_CREATE');

        $user    = $this->getUser();
        $project = new Project();
        $role    = $this->getRepository('GitonomyCoreBundle:Role')->findOneByName('Lead developer');
        $form    = $this->createForm('project', $project);
        $request = $this->getRequest();

        $project->getUserRoles()->add(new UserRoleProject($user, $project, $role));

        if ('GET' === $request->getMethod()) {
            return $this->render('GitonomyWebsiteBundle:Project:create.html.twig', array(
                'form' => $form->createView()
            ));
        }

        $form->bind($request);

        if ($form->isValid()) {
            $this->persistEntity($project);

            $this->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($project));

            $this->setFlash('success', $this->trans('notice.project_created', array(), 'project'));

            return $this->redirect($this->generateUrl('project_newsfeed', array('slug' => $project->getSlug())));
        }

        $this->setFlash('error', $this->trans('error.form_invalid', array(), 'register'));

        return $this->render('GitonomyWebsiteBundle:Project:create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newsfeedAction(Request $request, $slug)
    {
        $reference  = $request->query->get('reference');
        $project    = $this->getProject($slug);
        $messages   = $this->getRepository('GitonomyCoreBundle:Message')->findByProject($project, $reference);

        if ($project->isEmpty()) {
            return $this->render('GitonomyWebsiteBundle:Project:empty.html.twig', array(
                'project'    => $project
            ));
        }

        return $this->render('GitonomyWebsiteBundle:Project:newsfeed.html.twig', array(
            'project'    => $project,
            'messages'   => $messages,
            'reference'  => $reference,
        ));
    }

    public function historyAction(Request $request, $slug)
    {
        $reference  = $request->query->get('reference', null);
        $project    = $this->getProject($slug);
        $repository = $project->getRepository();
        $log        = $repository->getLog($reference);

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        $references    = $repository->getReferences();
        $referenceName = function (Reference $reference) {
            return $reference->getName();
        };

        return $this->render('GitonomyWebsiteBundle:Project:history.html.twig', array(
            'project'       => $project,
            'reference'     => $reference,
            'pager'         => $pager
        ));
    }

    /**
     * Displays a commit.
     */
    public function commitAction($slug, $hash)
    {
        $project    = $this->getProject($slug);
        $commit     = $project->getRepository()->getCommit($hash);

        return $this->render('GitonomyWebsiteBundle:Project:commit.html.twig', array(
            'project'    => $project,
            'reference'  => $project->getDefaultBranch(),
            'commit'     => $commit
        ));
    }

    /**
     * Displays tree
     *
     * @var string $reference Can be a branch name or a commit hash
     */
    public function treeAction($slug, $reference, $path)
    {
        $project    = $this->getProject($slug);
        $repository = $project->getRepository();
        $revision   = $repository->getRevision($reference);
        $commit     = $revision->getResolved();

        if ($repository->getReferences()->hasBranch($reference)) {
            $branch = $reference;
        } else {
            $branch = $project->getDefaultBranch();
        }

        $tree = $commit->getTree();
        if (strlen($path) > 0 && 0 === substr($path, 0, 1)) {
            $path = substr($path, 1);
        }

        try {
            $element = $tree->resolvePath($path);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $parameters = array(
            'reference'     => $reference,
            'commit'        => $commit,
            'project'       => $project,
            'parent_path'   => $path === '' ? null : substr($path, 0, strrpos($path, '/')),
            'path'          => $path,
            'path_exploded' => explode('/', $path),
        );


        if ($element instanceof Blob) {
            $parameters['blob'] = $element;
            $tpl = 'GitonomyWebsiteBundle:Project:blob.html.twig';
        } elseif ($element instanceof Tree) {
            $parameters['tree'] = $element;
            $tpl = 'GitonomyWebsiteBundle:Project:tree.html.twig';
        }

        return $this->render($tpl, $parameters);
    }

    public function branchesAction(Request $request, $slug)
    {
        $project   = $this->getProject($slug);
        $reference = $request->query->get('reference', $project->getDefaultBranch());

        return $this->render('GitonomyWebsiteBundle:Project:branches.html.twig', array(
            'project'   => $project,
            'reference' => $reference,
        ));
    }

    public function tagsAction($slug)
    {
        $project    = $this->getProject($slug);
        $tags       = $project->getRepository()->getReferences()->getTags();

        usort($tags, function($left, $right) {
            return $left->getCommit()->getAuthorDate() < $right->getCommit()->getAuthorDate();
        });

        return $this->render('GitonomyWebsiteBundle:Project:tags.html.twig', array(
            'project' => $project,
            'tags'    => $tags,
        ));
    }

    /**
     * Displays tree history.
     */
    public function treeHistoryAction(Request $request, $slug, $reference, $path)
    {
        $project    = $this->getProject($slug);
        $repository = $project->getRepository();
        $branch     = $repository->getReferences()->getBranch($reference);
        $log        = $repository->getLog($branch->getCommitHash(), $path);
        $branches   = $repository->getReferences()->getBranches();

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        return $this->render('GitonomyWebsiteBundle:Project:treeHistory.html.twig', array(
            'reference'     => $reference,
            'log'           => $log,
            'project'       => $project,
            'path'          => $path,
            'path_exploded' => explode('/', $path),
            'page'          => $page,
            'pager'         => $pager,
            'branches'      => $branches,
        ));
    }

    public function blameAction(Request $request, $slug, $reference, $path)
    {
        $project = $this->getProject($slug);

        $repository = $project->getRepository();

        $resolved = $repository->getRevision($reference)->getResolved()->getTree()->resolvePath($path);

        if (!$resolved instanceof Blob || $resolved->isBinary()) {
            throw $this->createNotFoundException('Cannot blame a tree or binary');
        }

        $blame = $repository->getBlame($reference, $path);

        return $this->render('GitonomyWebsiteBundle:Project:blame.html.twig', array(
            'project'       => $project,
            'blame'         => $blame,
            'reference'     => $reference,
            'path'          => $path,
            'path_exploded' => explode('/', $path)
        ));
    }

    public function compareAction($slug, $revision)
    {
        $project = $this->getProject($slug);
        $log     = $project->getRepository()->getLog($revision);

        return $this->render('GitonomyWebsiteBundle:Project:compare.html.twig', array(
            'project'   => $project,
            'revision' => $revision,
            'log'       => $log,
            'diff'      => $log->getDiff()
        ));
    }

    public function permissionsAction($slug)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $roleForm      = $this->createForm('project_role', null, array('usedUsers' => $project->getUsers()));
        $gitAccessForm = $this->createForm('project_git_access');

        return $this->render('GitonomyWebsiteBundle:Project:permissions.html.twig', array(
            'project'       => $project,
            'roleForm'      => $roleForm->createView(),
            'gitAccessForm' => $gitAccessForm->createView(),
            'token'         => $this->createToken('project_permissions')
        ));
    }

    public function postPermissionsCreateRoleAction(Request $request, $slug)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $role          = new UserRoleProject(null, $project);
        $roleForm      = $this->createForm('project_role', $role, array('usedUsers' => $project->getUsers()));
        $gitAccessForm = $this->createForm('project_git_access');

        if ($roleForm->bind($request)->isValid()) {
            $this->persistEntity($role);
            $this->setFlash('success', $this->trans('notice.role_created', array(), 'project_permissions'));

            return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
        }

        return $this->render('GitonomyWebsiteBundle:Project:permissions.html.twig', array(
            'project'       => $project,
            'roleForm'      => $roleForm->createView(),
            'gitAccessForm' => $gitAccessForm->createView(),
            'token'         => $this->createToken('project_permissions')
        ));
    }

    public function postPermissionsDeleteRoleAction(Request $request, $slug, $id)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $role = $this->getRepository('GitonomyCoreBundle:UserRoleProject')->find($id);

        if ($role->getProject()->getSlug() !== $project->getSlug()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isTokenValid('project_permissions', $request->query->get('_token'))) {
            $this->setFlash('error', $this->trans('error.token_invalid', array(), 'project_permissions'));

            return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
        }

        $em = $this->get('doctrine')->getEntityManager();
        $em->remove($role);
        $em->flush();
        $this->setFlash('success', $this->trans('notice.role_deleted', array(), 'project_permissions'));

        return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
    }

    public function postPermissionsCreateGitAccessAction(Request $request, $slug)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $gitAccess     = new ProjectGitAccess($project);
        $roleForm      = $this->createForm('project_role', null, array('usedUsers' => $project->getUsers()));
        $gitAccessForm = $this->createForm('project_git_access', $gitAccess);

        if ($gitAccessForm->bind($request)->isValid()) {
            $this->persistEntity($gitAccess);
            $this->setFlash('success', $this->trans('notice.git_access_created', array(), 'project_permissions'));

            return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
        }

        return $this->render('GitonomyWebsiteBundle:Project:permissions.html.twig', array(
            'project'       => $project,
            'roleForm'      => $roleForm->createView(),
            'gitAccessForm' => $gitAccessForm->createView()
        ));
    }

    public function postPermissionsDeleteGitAccessAction(Request $request, $slug, $id)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $role    = $this->getRepository('GitonomyCoreBundle:ProjectGitAccess')->find($id);

        if ($role->getProject()->getSlug() !== $project->getSlug()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isTokenValid('project_permissions', $request->query->get('_token'))) {
            $this->setFlash('error', $this->trans('error.token_invalid', array(), 'project_permissions'));

            return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
        }

        $em = $this->get('doctrine')->getEntityManager();
        $em->remove($role);
        $em->flush();

        $this->setFlash('success', $this->trans('notice.git_access_deleted', array(), 'project_permissions'));

        return $this->redirect($this->generateUrl('project_permissions', array('slug' => $slug)));
    }

    public function adminAction(Request $request, $slug)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        $viewProject = clone $project;
        $form        = $this->createForm('project', $project, array('action' => 'edit'));

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->flush();
            $this->setFlash('success', $this->trans('notice.informations_saved', array(), 'project_admin'));

            return $this->redirect($this->generateUrl('project_admin', array('slug' => $slug)));
        }

        return $this->render('GitonomyWebsiteBundle:Project:admin.html.twig', array(
            'form'       => $form->createView(),
            'project'    => $viewProject
        ));
    }

    public function deleteAction(Request $request, $slug)
    {
        $this->assertGranted('PROJECT_ADMIN', $project = $this->getProject($slug));

        if (!$this->isTokenValid('project_delete', $request->query->get('_token'))) {
            $this->setFlash('error', $this->trans('error.token_invalid', array(), 'project_permissions'));

            return $this->redirect($this->generateUrl('project_admin', array('slug' => $slug)));
        }

        $this->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($project));
        $this->removeEntity($project);

        $this->setFlash('success', $this->trans('notice.deleted', array(), 'project_admin'));

        return $this->redirect($this->generateUrl('project_list'));
    }

    /**
     * @return Project
     */
    protected function getProject($slug)
    {
        $user = $this->getUser();

        $project = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);
        if (null === $project) {
            throw $this->createNotFoundException(sprintf('Project with slug "%s" not found', $slug));
        }

        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('PROJECT_READ', $project)) {
            throw $this->createAccessDeniedException('You are not contributor of the project');
        }

        return $project;
    }
}
