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
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class ProjectController extends BaseController
{
    /**
     * Displays the project main page
     */
    public function showAction(Request $request, $slug)
    {
        $project    = $this->getProject($slug);
        $reference  = $request->query->get('reference');
        $repository = $this->getGitRepository($project);

        $references = $repository->getReferences();

        if (null !== $reference && $references->hasBranch($reference)) {
            $master = $references->getBranch($reference);
            $activity = $this->getBranchesActivity($repository, $master);
        } elseif ($references->hasBranches()) {
            $reference = $project->getDefaultBranch();
            $master = $references->getBranch($reference);
            $activity = $this->getBranchesActivity($repository, $master);
        } else {
            return $this->render('GitonomyFrontendBundle:Project:showEmpty.html.twig', array(
                'project' => $project
            ));
        }


        return $this->render('GitonomyFrontendBundle:Project:show.html.twig', array(
            'project'           => $project,
            'repository'        => $repository,
            'reference'         => $reference,
            'branches_activity' => $activity
        ));
    }

    public function historyAction(Request $request, $slug)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $reference  = $request->query->get('reference');

        $log = $repository->getLog($reference);

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

        return $this->render('GitonomyFrontendBundle:Project:history.html.twig', array(
            'project'    => $project,
            'reference'  => $reference,
            'repository' => $repository,
            'pager'      => $pager,
            'data'       => array_map($convert, (array) $pager->getResults())
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
            'reference'  => $project->getDefaultBranch(),
            'commit'     => $commit
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
            'path_exploded' => explode('/', $path)
        );

        if ($element instanceof Blob) {
            $parameters['blob'] = $element;
            $tpl = 'GitonomyFrontendBundle:Project:showBlob.html.twig';
        } elseif ($element instanceof Tree) {
            $parameters['tree'] = $element;
            $tpl = 'GitonomyFrontendBundle:Project:showTree.html.twig';
        }

        return $this->render($tpl, $parameters);
    }

    /**
     * Displays tree history.
     */
    public function showTreeHistoryAction(Request $request, $slug, $reference, $path)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $branch     = $repository->getReferences()->getBranch($reference);
        $log        = $repository->getLog($branch->getCommitHash(), $path);

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage(50);
        $pager->setPage($page = $request->query->get('page', 1));

        return $this->render('GitonomyFrontendBundle:Project:showTreeHistory.html.twig', array(
            'reference'     => $reference,
            'log'           => $log,
            'project'       => $project,
            'repository'    => $repository,
            'parent_path'   => $path === '' ? null : substr($path, 0, strrpos($path, '/')),
            'path'          => $path,
            'path_exploded' => explode('/', $path),
            'page'          => $page,
            'pager'         => $pager
        ));
    }

    public function _showFeedAction($slug, $reference)
    {
        $project    = $this->getProject($slug);
        $repository = $this->getGitRepository($project);
        $feed       = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Feed')->findOneBy(array(
            'project'   => $project,
            'reference' => 'refs/heads/'.$reference
        ));

        if (null === $feed) {
            throw $this->createNotFoundException(sprintf('Feed "%s" not found', $reference));
        }

        return $this->render('GitonomyFrontendBundle:Project:_showFeed.html.twig', array(
            'project'    => $project,
            'feed'       => $feed,
            'repository' => $repository,
            'reference'  => $reference,
        ));
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
            if ($branch == $against) {
                continue;
            }

            $logBehind = $repository->getLog($branch->getFullname().'..'.$against->getFullname());
            $logAbove = $repository->getLog($against->getFullname().'..'.$branch->getFullname());

            $rows[] = array(
                'branch'           => $branch,
                'above'            => count($logAbove->getCommits()),
                'behind'           => count($logBehind->getCommits()),
                'lastModification' => $branch->getLastModification()
            );
        }

        usort($rows, function ($left, $right) {
            return $left['lastModification']->getTimestamp() < $right['lastModification']->getTimestamp();
        });

        return $rows;
    }
}
