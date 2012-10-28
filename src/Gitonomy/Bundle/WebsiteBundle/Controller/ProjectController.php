<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Git\Blob;
use Gitonomy\Git\Tree;
use Gitonomy\Git\Reference;
use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Component\Pagination\Adapter\GitLogAdapter;

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
        $reference  = $this->getRequest()->query->get('reference');
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $messages   = $this->getRepository('GitonomyCoreBundle:Message')->findByProject($project, $reference);
        $branches   = $this->getGitRepository($project)->getReferences()->getBranches();

        return $this->render('GitonomyWebsiteBundle:Project:newsfeed.html.twig', array(
            'project'    => $project,
            'branches'   => $branches,
            'messages'   => $messages,
            'repository' => $repository,
            'reference'  => $reference,
        ));
    }

    public function historyAction($slug, $reference = null, $path = null)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $request    = $this->getRequest();
        $reference  = $request->query->get('reference');
        $log        = $repository->getLog($reference);
        $branches   = $this->getGitRepository($project)->getReferences()->getBranches();

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);

        $references = $repository->getReferences();
        $referenceName = function (Reference $reference) {
            return $reference->getName();
        };

        $convert = function ($commit) use ($references, $referenceName) {
            return array(
                'hash'          => $commit->getHash(),
                'short_message' => $commit->getShortMessage(),
                'parents'       => $commit->getParentHashes(),
                'tags'          => array_map($referenceName, $references->resolveTags($commit)),
                'branches'      => array_map($referenceName, $references->resolveBranches($commit)),
            );
        };

        return $this->render('GitonomyWebsiteBundle:Project:history.html.twig', array(
            'project'    => $project,
            'reference'  => $reference,
            'repository' => $repository,
            'pager'      => $pager,
            'data'       => array_map($convert, (array) $pager->getResults()),
            'parent_path'   => $path === '' ? null : substr($path, 0, strrpos($path, '/')),
            'path'          => $path,
            'path_exploded' => explode('/', $path),
            'branches'      => $branches,
        ));
    }

    /**
     * Displays a commit.
     */
    public function commitAction($slug, $hash)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $commit     = $repository->getCommit($hash);

        return $this->render('GitonomyWebsiteBundle:Project:commit.html.twig', array(
            'project'    => $project,
            'repository' => $repository,
            'reference'  => $project->getDefaultBranch(),
            'commit'     => $commit
        ));
    }

    /**
     * Displays tree
     */
    public function treeAction($slug, $reference, $path)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $branches   = $this->getGitRepository($project)->getReferences()->getBranches();

        $revision = $repository->getRevision($reference);
        $commit = $revision->getResolved();
        if ($repository->getReferences()->hasBranch($reference)) {
            $branch = $reference;
        } else {
            $branch = $project->getDefaultBranch();
        }

        $tree = $commit->getTree();
        if (strlen($path) > 0 && 0 === substr($path, 0, 1)) {
            $path = substr($path, 1);
        }

        $element = $tree->resolvePath($path);

        $parameters = array(
            'reference'     => $reference,
            'branch'        => $branch,
            'commit'        => $commit,
            'project'       => $project,
            'repository'    => $repository,
            'parent_path'   => $path === '' ? null : substr($path, 0, strrpos($path, '/')),
            'path'          => $path,
            'path_exploded' => explode('/', $path),
            'branches'      => $branches,
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

    public function branchesAction($slug)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $branches   = $repository->getReferences()->getBranches();

        usort($branches, function($left, $right) {
            return $left->getCommit()->getAuthorDate() < $right->getCommit()->getAuthorDate();
        });

        return $this->render('GitonomyWebsiteBundle:Project:branches.html.twig', array(
            'project'  => $project,
            'branches' => $branches,
        ));
    }

    public function tagsAction($slug)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $tags       = $repository->getReferences()->getTags();

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
        $repository = $this->getGitRepository($project);
        $branch     = $repository->getReferences()->getBranch($reference);
        $log        = $repository->getLog($branch->getCommitHash(), $path);
        $branches   = $this->getGitRepository($project)->getReferences()->getBranches();

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        return $this->render('GitonomyWebsiteBundle:Project:treeHistory.html.twig', array(
            'reference'     => $reference,
            'log'           => $log,
            'project'       => $project,
            'repository'    => $repository,
            'parent_path'   => $path === '' ? null : substr($path, 0, strrpos($path, '/')),
            'path'          => $path,
            'path_exploded' => explode('/', $path),
            'page'          => $page,
            'pager'         => $pager,
            'branches'      => $branches,
        ));
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

        if (!$this->get('security.context')->isGranted('PROJECT_CONTRIBUTE', $project)) {
            throw $this->createAccessDeniedException('You are not contributor of the project');
        }

        return $project;
    }
}
