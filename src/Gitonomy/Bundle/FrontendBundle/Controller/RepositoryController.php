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
     * Show a repository
     */
    public function showAction($id, $reference = null)
    {
        $repository = $this->getRepository($id);

        $reference = null === $reference ? $repository->getMainBranch() : $reference;

        return $this->render('GitonomyFrontendBundle:Repository:show.html.twig', array(
            'repository' => $repository,
            'reference'  => $reference
        ));
    }

    /**
     * Displays a commit.
     */
    public function showCommitAction($id, $hash)
    {
        $repository = $this->getRepository($id);

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

    public function blockNavigationAction($id)
    {
        $repository = $this->getRepository($id);

        $gitRepository = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
        ;

        return $this->render('GitonomyFrontendBundle:Repository:blockNavigation.html.twig', array(
            'repository'    => $repository,
            'gitRepository' => $gitRepository
        ));
    }

    /**
     * Displays last commit of a repository.
     *
     * @todo Separate two cases: the requested revision does not exists and no commit.
     */
    public function blockCommitHistoryAction($id, $reference = null, $limit = 10)
    {
        $repository = $this->getRepository($id);

        $revision = null === $reference ? $repository->getMainBranch() : $reference;

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
        $repository = $this->getRepository($id);

        $branches = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getReferences()
            ->getBranches()
        ;

        return $this->render('GitonomyFrontendBundle:Repository:blockBranches.html.twig', array(
            'branches'   => $branches,
            'repository' => $repository
        ));
    }

    public function blockTagsAction($id)
    {
        $repository = $this->getRepository($id);

        $tags = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getReferences()
            ->getTags()
        ;

        return $this->render('GitonomyFrontendBundle:Repository:blockTags.html.twig', array(
            'tags'       => $tags,
            'repository' => $repository
        ));
    }

    protected function getRepository($id)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        return $repository;
    }
}
