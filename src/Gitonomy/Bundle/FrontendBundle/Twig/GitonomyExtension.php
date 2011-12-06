<?php

namespace Gitonomy\Bundle\FrontendBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Repository;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class GitonomyExtension extends \Twig_Extension
{
    protected $container;
    protected $sshAccess;

    function __construct(ContainerInterface $container, $sshAccess)
    {
        $this->container = $container;
        $this->sshAccess = $sshAccess;
    }

    public function getFunctions()
    {
        return array(
            'repository_ssh'  => new \Twig_Function_Method($this, 'getRepositorySsh'),
            'repository_path' => new \Twig_Function_Method($this, 'getRepositoryPath'),
            'user_list'       => new \Twig_Function_Method($this, 'getUserList')
        );
    }

    public function getTests()
    {
        return array(
            'forked' => new \Twig_Test_Method($this, 'isRepositoryForked')
        );
    }

    public function getName()
    {
        return 'gitonomy';
    }

    public function isRepositoryForked(Repository $repository)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->container->get('gitonomy_core.git.repository_pool')->exists($user->getUsername(), $repository->getName());
    }

    public function getRepositorySsh(Repository $repository)
    {
        return $this->sshAccess.':'.$repository->getNamespace().'/'.$repository->getName().'.git';
    }

    public function getRepositoryPath(Repository $repository)
    {
        return $this->container->get('router')->generate('gitonomy_frontend_repository_show', array(
            'namespace' => $repository->getNamespace(),
            'name'      => $repository->getName()
        ));
    }

    public function getUserList()
    {
        return $this->container->get('doctrine')->getRepository('GitonomyCoreBundle:User')->findAll();
    }
}
