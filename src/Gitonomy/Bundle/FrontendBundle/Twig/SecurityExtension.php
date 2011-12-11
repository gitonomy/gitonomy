<?php

namespace Gitonomy\Bundle\FrontendBundle\Twig;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Gitonomy\Bundle\FrontendBundle\Security\Right;

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

    public function isGranted($role, $object = null, $field = null)
    {
        $token = $this->context->getToken();

        try {
            return $this->right->isGranted($token, $role, $object);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFunctions()
    {
        return array(
            'is_granted' => new \Twig_Function_Method($this, 'isGranted'),
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
