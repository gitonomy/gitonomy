<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Controller for repository actions.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class RepositoryController extends BaseController
{
    /**
     * Displays a commit.
     */
    public function showCommitAction($id, $hash)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        $commit = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getCommit($hash)
        ;

        return $this->render('GitonomyFrontendBundle:Repository:showCommit.html.twig', array(
            'commit'     => $commit,
            'repository' => $repository
        ));
    }

    /**
     * Displays last commit of a repository.
     *
     * @todo Separate two cases: the requested revision does not exists and no commit.
     */
    public function blockCommitHistoryAction($id, $revision = 'HEAD', $limit = 10)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        $revision = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getRevision($revision)
        ;

        try {
            $revision->getResolved();

            $commits = $revision->getLog($limit)->getCommits();

            return $this->render('GitonomyFrontendBundle:Repository:blockCommitHistory.html.twig', array(
                'commits'    => $commits,
                'repository' => $repository
            ));
        } catch (\RuntimeException $e) {
            return $this->render('GitonomyFrontendBundle:Repository:blockCommitHistoryEmpty.html.twig', array(
                'repository' => $repository
            ));
        }
    }

    public function blockBranchesAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        $branches = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getBranches()
        ;

        return $this->render('GitonomyFrontendBundle:Repository:blockBranches.html.twig', array(
            'branches'   => $branches,
            'repository' => $repository
        ));
    }
}
