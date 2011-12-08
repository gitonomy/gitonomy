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

    public function getGlobals()
    {
        return array(
            'gitonomy' => array(
                'name'              => $this->container->getParameter('gitonomy_frontend.project.name'),
                'open_registration' => $this->container->getParameter('gitonomy_frontend.user.open_registration')
            )
        );
    }

    public function getFunctions()
    {
        return array(
            'gravatar' => new \Twig_Function_Method($this, 'getGravatar')
        );
    }

    public function getName()
    {
        return 'gitonomy';
    }

    public function getGravatar($email, $size = 100)
    {
        return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size;
    }

    public function getUserList()
    {
        return $this->container->get('doctrine')->getRepository('GitonomyCoreBundle:User')->findAll();
    }
}
