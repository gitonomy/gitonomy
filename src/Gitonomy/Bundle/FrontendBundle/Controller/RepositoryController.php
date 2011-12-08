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
    public function blockCommitHistoryAction($id, $revision = 'HEAD', $limit = 10)
    {
        $repository = $this->getDoctrine()->getRepository('GitonomyCoreBundle:Repository')->find($id);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('Repository #%s not found', $id));
        }

        $commits = array();
        $commit = $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($repository)
            ->getRevision($revision)
            ->getCommit()
        ;

        while ($limit > 0) {
            $commits[] = $commit;
            $commit = $commit->getParents();
            if (!count($commit)) {
                break;
            }
            $commit = $commit[0];
            $limit--;
        }

        return $this->render('GitonomyFrontendBundle:Repository:blockCommitHistory.html.twig', array(
            'commits' => $commits
        ));
    }
}
