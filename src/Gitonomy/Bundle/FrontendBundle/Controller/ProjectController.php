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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Component\Pagination\Adapter\GitLogAdapter;
use Gitonomy\Component\Git\Graph\Map;
use Gitonomy\Git\Tree;
use Gitonomy\Git\Blob;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Reference;

/**
 * Controller for project displaying.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectController extends BaseController
{
    /**
     * Displays the navigation bar
     */
    public function blockNavigationAction(Request $request, Project $project)
    {
        $reference = $request->attributes->get('reference', 'master');
        $routeName       = $request->attributes->get('route_name', 'gitonomyfrontend_project_show');

        $routeParameters = $request->attributes->get('route_parameters', array(
            'slug'      => $project->getSlug(),
            'reference' => $reference
        ));

        $repository = $this->getGitRepository($project);
        $references = $repository->getReferences();

        if ($reference) {
            $branch = $references->getBranch($reference);
        } else {
            $branch = $references->getBranch('master');
        }
        $activity = $this->getBranchesActivity($repository, $branch);

        return $this->render('GitonomyFrontendBundle:Project:blockNavigation.html.twig', array(
            'project'          => $project,
            'repository'       => $repository,
            'reference'        => $reference,
            'branches'         => $activity,
            'route_name'       => $routeName,
            'route_parameters' => $routeParameters,
            'active'           => $request->attributes->get('active', 'project')
        ));
    }

    /**
     * Displays the project main page
     */
    public function showAction(Request $request, $slug)
    {
        $project    = $this->getProject($slug);
        $reference  = $request->query->get('reference', 'master');
        $repository = $this->getGitRepository($project);

        $references = $repository->getReferences();

        if (!$references->hasBranches()) {
            return $this->render('GitonomyFrontendBundle:Project:showEmpty.html.twig', array(
                'project' => $project
            ));
        }

        return $this->render('GitonomyFrontendBundle:Project:show.html.twig', array(
            'project'           => $project,
            'repository'        => $repository,
            'reference'         => $reference
        ));
    }

    public function historyAction(Request $request, $slug)
    {
        $project    = $this->getProject($slug);
        $reference  = $request->query->get('reference', 'master');
        $repository = $this->getGitRepository($project);

        $commits = $repository
            ->getLog(null)
            ->setOffset($request->query->get('offset', 0))
            ->setLimit($request->query->get('limit', 50))
            ->getCommits()
        ;

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

        return $this->render('GitonomyFrontendBundle:Project:history.html.twig', array(
            'project'    => $project,
            'reference'  => $reference,
            'repository' => $repository,
            'commits'    => $commits,
            'data'       => array_map($convert, $commits)
        ));
    }

    /**
     * Displays a commit.
     */
    public function showCommitAction($slug, $hash)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $commit     = $repository->getCommit($hash);

        return $this->render('GitonomyFrontendBundle:Project:showCommit.html.twig', array(
            'project'    => $project,
            'repository' => $repository,
            'reference'  => $hash,
            'commit'     => $commit
        ));
    }

    public function showLastCommitsAction(Request $request, $slug, $reference)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);

        $log = $repository->getLog($reference);

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        return $this->render('GitonomyFrontendBundle:Project:showLastCommits.html.twig', array(
            'pager'      => $pager,
            'reference'  => $reference,
            'project'    => $project,
            'repository' => $repository,
            'page'       => $page
        ));
    }

    /**
     * Displays tree
     */
    public function showTreeAction($slug, $reference, $path)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);

        $revision = $repository->getRevision($reference);
        $commit = $revision->getResolved();

        $tree = $commit->getTree();
        if (strlen($path) > 0 && 0 === substr($path, 0, 1)) {
            $path = substr($path, 1);
        }

        try {
            $element = $tree->resolvePath($path);
        } catch (\InvalidArgumentException $e) {
            $element = null;
        }

        $code = 200;
        $parameters = array(
            'reference'  => $reference,
            'commit'     => $commit,
            'project'    => $project,
            'repository' => $repository,
            'path'       => $path,
        );

        if ($element instanceof Blob) {
            $parameters['blob'] = $element;
            $tpl = 'GitonomyFrontendBundle:Project:showBlob.html.twig';
        } elseif ($element instanceof Tree) {
            $parameters['tree'] = $element;
            $tpl = 'GitonomyFrontendBundle:Project:showTree.html.twig';
        } else {
            $tpl = 'GitonomyFrontendBundle:Project:showTreeNotFound.html.twig';
            $code = 404;
        }

        $response = $this->render($tpl, $parameters);
        $response->setStatusCode($code);

        return $response;
    }

    /**
     * @return Repository
     */
    protected function getGitRepository(Project $project)
    {
        return $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
        ;
    }

    /**
     * @return Project
     */
    protected function getProject($slug)
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('You must be connected to access a project');
        }

        $project = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);
        if (null === $project) {
            throw $this->createNotFoundException(sprintf('Project with slug "%s" not found', $slug));
        }

        if (!$this->get('security.context')->isGranted('PROJECT_CONTRIBUTE', $project)) {
            throw new AccessDeniedException('You are not contributor of the project');
        }

        return $project;
    }

    protected function getBranchesActivity(Repository $repository, Reference $against)
    {
        $rows = array();
        $references = $repository->getReferences();

        foreach ($references->getBranches() as $branch) {
            $logBehind = $repository->getLog($branch->getCommitHash().'..'.$against->getCommitHash());
            $logAbove = $repository->getLog($against->getCommitHash().'..'.$branch->getCommitHash());

            $rows[] = array(
                'branch' => $branch,
                'above'  => count($logAbove->getCommits()),
                'behind' => count($logBehind->getCommits()),
                'commit' => $branch->getCommit()
            );
        }

        usort($rows, function ($left, $right) {
            $l = $left['commit']->getAuthorDate()->getTimestamp();
            $r = $right['commit']->getAuthorDate()->getTimestamp();

            return $l < $r;
        });

        return $rows;
    }
}
