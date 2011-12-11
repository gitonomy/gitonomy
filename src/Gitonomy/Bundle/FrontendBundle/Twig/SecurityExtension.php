<?php

namespace Gitonomy\Bundle\FrontendBundle\Twig;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Gitonomy\Bundle\FrontendBundle\Security\Right;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * SecurityExtension exposes security context features.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class SecurityExtension extends \Twig_Extension
{
    protected $context;
    protected $right;

    public function __construct(SecurityContextInterface $context)
    {
        $this->context = $context;
    }

    public function setRight(Right $right)
    {
        $this->right = $right;
    }

    public function isGranted($permission)
    {
        $user = $this->context->getToken()->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $this->right->isGranted($user, $permission);
    }

    public function isGrantedForProject(Project $project, $permission)
    {
        $user = $this->context->getToken()->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $this->right->isGrantedForProject($user, $project, $permission);
    }

    public function getFunctions()
    {
        return array(
            'is_granted'             => new \Twig_Function_Method($this, 'isGranted'),
            'is_granted_for_project' => new \Twig_Function_Method($this, 'isGrantedForProject'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'gitonomy.security';
    }
}
