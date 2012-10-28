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

namespace Gitonomy\Bundle\WebsiteBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Git\Tree;

class GitonomyExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'gravatar' => new \Twig_Function_Method($this, 'getGravatar'),
        );
    }

    public function getName()
    {
        return 'gitonomy';
    }

    public function getGravatar($email, $size = 50)
    {
        if ($email === null) {
            return '<span class="lsf">user</span>';
        }

        return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size;
    }
}
