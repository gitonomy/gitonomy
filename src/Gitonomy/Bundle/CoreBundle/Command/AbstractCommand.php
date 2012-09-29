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

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    public function getProject($projectSlug)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $project  = $doctrine->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);

        if (null === $project) {
            throw new \RuntimeException(sprintf('Project with slug "%s" not found', $projectSlug));
        }

        return $project;
    }

    public function getUser($username)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $user     = $doctrine->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (null === $user) {
            throw new \RuntimeException(sprintf('User "%s" not found', $username));
        }

        return $user;
    }

    public function getRole($slug)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $role     = $doctrine->getRepository('GitonomyCoreBundle:Role')->findOneBySlug($slug);

        if (null === $role) {
            throw new \RuntimeException(sprintf('Role "%s" not found', $slug));
        }

        return $role;
    }
}
