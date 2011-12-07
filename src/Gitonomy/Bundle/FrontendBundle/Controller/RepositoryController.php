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
     * Displays a repository.
     */
    public function showAction($namespace, $name)
    {
        $repository = $this->getRepositoryPool()->find($namespace, $name);

        if (null === $repository) {
            throw $this->createNotFoundException(sprintf('The repository %s/%s does not exists', $namespace, $name));
        }

        return $this->render('GitonomyFrontendBundle:Repository:show.html.twig', array(
            'repository' => $repository
        ));
    }

    /**
     * Forks a repository.
     *
     * @todo Add CSRF
     */
    public function forkAction($namespace, $name)
    {
        $this->assertPermission('ROLE_USER');
        $user = $this->getUser();
        $username = $user->getUsername();

        if ($username === $namespace) {
            throw new \LogicException('User is trying to fork his own repository');
        }

        $pool = $this->getRepositoryPool();

        if ($pool->exists($username, $name)) {
            throw new \LogicException('Repository already exists');
        }

        $pool->fork($user, $pool->findOneByNamespaceAndName($namespace, $name));

        return $this->redirect($this->generateUrl('gitonomyfrontend_repository_show', array('namespace' => $username, 'name' => $name)));
    }

    /**
     * @return \Gitonomy\Bundle\CoreBundle\Git\RepositoryPool
     */
    protected function getRepositoryPool()
    {
        return $this->container->get('gitonomy_core.git.repository_pool');
    }
}
