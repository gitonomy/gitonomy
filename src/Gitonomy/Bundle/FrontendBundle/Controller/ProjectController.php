<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Component\Pagination\Adapter\GitLogAdapter;
use Gitonomy\Component\Git\Graph\Map;
use Gitonomy\Git\Tree;
use Gitonomy\Git\Blob;

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
    public function showAction(Request $request, $slug)
    {
        $project = $this->getProject($slug);
        $reference = $request->query->get('reference', 'master');

        return $this->render('GitonomyFrontendBundle:Project:show.html.twig', array(
            'project'   => $project,
            'reference' => $reference
        ));
    }

    public function historyAction(Request $request, $slug)
    {
        $project = $this->getProject($slug);
        $reference = $request->query->get('reference');

        $repository = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
        ;

        $references = $repository->getReferences();

        $commits = $repository
            ->getLog($reference)
            ->setLimit($request->query->get('limit', 50))
            ->getCommits()
        ;

        $data = array();
        foreach ($commits as $commit) {
            $data[] = array(
                'hash'          => $commit->getHash(),
                'short_message' => $commit->getShortMessage(),
                'parents'       => $commit->getParentHashes(),
                'tags'          => $references->resolveTags($commit->getHash()),
                'branches'      => $references->resolveBranches($commit->getHash()),
            );
        }

        return $this->render('GitonomyFrontendBundle:Project:history.html.twig', array(
            'project'   => $project,
            'reference' => $reference,
            'commits'   => $commits,
            'data'      => $data
        ));
    }

    private function getInfos($commit)
    {
        return array(
            'id'      => substr($commit->getHash(), 0, 12),
            'message' => $commit->getShortMessage()
        );
    }

    /**
     * Displays the last commits
     */
    public function showLastCommitsAction(Request $request, $slug, $reference)
    {
        $project = $this->getProject($slug);

        return $this->render('GitonomyFrontendBundle:Project:showLastCommits.html.twig', array(
            'project'   => $project,
            'reference' => $reference,
            'page'      => $request->query->get('page', 1)
        ));
    }

    public function showTreeAction($slug, $reference, $path)
    {
        $project = $this->getProject($slug);

        return $this->render('GitonomyFrontendBundle:Project:showTree.html.twig', array(
            'project'   => $project,
            'reference' => $reference,
            'path'      => $path
        ));
    }

    /**
     * Displays a commit.
     */
    public function showCommitAction($slug, $hash)
    {
        $project = $this->getProject($slug);

        $commit = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
            ->getCommit($hash)
        ;

        return $this->render('GitonomyFrontendBundle:Project:showCommit.html.twig', array(
            'project' => $project,
            'commit'  => $commit
        ));
    }

    public function blockNavigationAction($slug, $active, $reference)
    {
        $project = $this->getProject($slug);

        $repository = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
        ;

        return $this->render('GitonomyFrontendBundle:Project:blockNavigation.html.twig', array(
            'project'    => $project,
            'repository' => $repository,
            'reference'  => $reference,
            'active'     => $active
        ));
    }

    /**
     * Displays last commit of a project.
     *
     * @todo Separate two cases: the requested revision does not exists and no commit.
     */
    public function blockCommitHistoryAction($slug, $reference = 'master', $perPage = 50, $page = 0)
    {
        $project = $this->getProject($slug);

        $log = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
            ->getLog($reference)
        ;

        $pager = new Pager(new GitLogAdapter($log));
        $pager->setPerPage((int) min(50, $perPage));
        $pager->setPage((int) $page);

        return $this->render('GitonomyFrontendBundle:Project:blockCommitHistory.html.twig', array(
            'pager' => $pager,
            'reference' => $reference,
            'project' => $project,
            'page' => $page
        ));
    }

    /**
     * Displays tree
     */
    public function blockTreeAction($slug, $reference, $path)
    {
        $project = $this->getProject($slug);

        $revision = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
            ->getRevision($reference)
        ;

        $revision->getResolved();
        $commit = $revision->getCommit();

        $tree = $commit->getTree();
        if (strlen($path) > 0 && 0 === substr($path, 0, 1)) {
            $path = substr($path, 1);
        }

        $element = $tree->resolvePath($path);

        if ($element instanceof Blob) {
            return $this->render('GitonomyFrontendBundle:Project:blockBlob.html.twig', array(
                'path'      => $path,
                'reference' => $reference,
                'blob'      => $element,
                'project'   => $project
            ));
        } elseif ($element instanceof Tree) {
            return $this->render('GitonomyFrontendBundle:Project:blockTree.html.twig', array(
                'commit'    => $commit,
                'path'      => $path,
                'reference' => $reference,
                'tree'      => $element,
                'project'   => $project
            ));
        }

        throw new \RuntimeException(sprintf('Unable to render element of type "%s"', get_class($element)));
    }

    public function blockBranchesActivityAction($slug, $reference)
    {
        $project = $this->getProject($slug);

        $repository = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
        ;

        $references = $repository->getReferences();

        $master = $references->getBranch($reference);

        $rows = array();
        foreach ($references->getBranches() as $branch) {
            if ($branch == $master) {
                continue;
            }

            $logBehind = $repository->getLog($branch->getFullname().'..'.$master->getFullname());
            $logAbove = $repository->getLog($master->getFullname().'..'.$branch->getFullname());

            $rows[] = array(
                'branch'           => $branch,
                'above'            => count($logAbove->getCommits()),
                'behind'           => count($logBehind->getCommits()),
                'lastModification' => $branch->getLastModification()
            );
        }

        return $this->render('GitonomyFrontendBundle:Project:blockBranchesActivity.html.twig', array(
            'main' => $master,
            'rows' => $rows
        ));
    }

    /**
     * @return Gitonomy\Bundle\CoreBundle\Entity\Project
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
}
