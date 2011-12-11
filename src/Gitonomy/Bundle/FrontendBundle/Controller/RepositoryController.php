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

    public function blockCommitHistoryAction($id, $revision = 'HEAD', $limit = 10)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        $commits = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getRevision($revision)
            ->getLog($limit)
            ->getCommits()
        ;

        return $this->render('GitonomyFrontendBundle:Repository:blockCommitHistory.html.twig', array(
            'commits'    => $commits,
            'repository' => $repository
        ));
    }
}
