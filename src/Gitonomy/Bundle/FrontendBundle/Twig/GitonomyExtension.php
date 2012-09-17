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

namespace Gitonomy\Bundle\FrontendBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Git\Tree;

class GitonomyExtension extends \Twig_Extension
{
    protected $container;
    protected $sshAccess;

    public function __construct(ContainerInterface $container, $sshAccess)
    {
        $this->container = $container;
        $this->sshAccess = $sshAccess;
    }

    public function getGlobals()
    {
        return array(
            'gitonomy' => array(
                'name'              => $this->container->getParameter('gitonomy_frontend.project.name'),
                'baseline'          => $this->container->getParameter('gitonomy_frontend.project.baseline'),
                'open_registration' => $this->container->getParameter('gitonomy_frontend.user.open_registration'),
                'locales'           => $this->container->getParameter('gitonomy_frontend.allowed_locales'),
            )
        );
    }

    public function getFunctions()
    {
        return array(
            'gravatar'        => new \Twig_Function_Method($this, 'getGravatar'),
            'project_list'    => new \Twig_Function_Method($this, 'getProjectList'),
            'user_list'       => new \Twig_Function_Method($this, 'getUserList'),
            'project_ssh'     => new \Twig_Function_Method($this, 'getProjectSsh'),
            'codemirror_mode' => new \Twig_Function_Method($this, 'getCodemirrorMode'),
            'git_type_action' => new \Twig_Function_Method($this, 'getGitTypeAction'),
        );
    }

    public function getTests()
    {
        return array(
            'git_tree'     => new \Twig_Test_Method($this, 'isGitTree'),
        );
    }

    public function getName()
    {
        return 'gitonomy';
    }

    public function isGitTree($tree)
    {
        return $tree instanceof Tree;
    }

    public function getProjectList()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user instanceof User) {
            return $this->container->get('doctrine')->getRepository('GitonomyCoreBundle:Project')->findByUser($user);
        } else {
            return array();
        }
    }

    public function getUserList()
    {
        return $this->container->get('doctrine')->getRepository('GitonomyCoreBundle:User')->findAll();
    }

    public function getGravatar($email, $size = 100)
    {
        return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size;
    }

    public function getProjectSsh(Project $project)
    {
        return sprintf('%s:%s.git', $this->sshAccess, $project->getSlug());
    }

    public function getCodemirrorMode($path)
    {
        switch (true) {
            case preg_match('#\.sh$#', $path):
                return 'shell';
            case preg_match('#\.json$#', $path):
                return 'javascript';
            case preg_match('#\.md$#', $path):
                return 'markdown';
            case preg_match('#\.xml$#', $path):
                return 'xml';
            case preg_match('#\.(yml|yaml)$#', $path):
                return 'yaml';
            case preg_match('#\.(php|php5|phtml)$#', $path):
                return 'php';
            case preg_match('#\.html(\.twig)?$#', $path):
                return 'htmlmixed';
            default:
                return 'text';
        }
    }

    public function getGitTypeAction($type)
    {
        switch ($type) {
            case 0:
                $action = 'created the thread by a push';
                break;
            case 1:
                $action = 'wrote';
                break;
            case 2:
                $action = 'closed';
                break;
            case 3:
                $action = 'commited';
                break;
            case 4:
                $action = 'asked a pull request';
                break;
            case 5:
                $action = 'merged';
                break;
            default:
                throw new \RuntimeException("Undefined type");
        }

        return $action;
    }
}
