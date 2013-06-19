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
use Gitonomy\Git\Blob;
use Gitonomy\Git\Reference;
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
            'gravatar'           => new \Twig_Function_Method($this, 'getGravatar'),
            'branches_activity'  => new \Twig_Function_Method($this, 'getBranchesActivity'),
        );
    }

    public function getGlobals()
    {
        return array(
            'config' => $this->container->get('gitonomy_core.config')
        );
    }

    public function getBranchesActivity(Project $project, $branch = null)
    {
        $repository = $project->getRepository();
        $references = $repository->getReferences();
        $branchName = null === $branch ? $project->getDefaultBranch() : $branch;

        $against = $references->getBranch($branchName);

        foreach ($references->getBranches() as $branch) {
            $logBehind = $repository->getLog($repository->getRevision($branch->getFullname().'..'.$against->getFullname()));
            $logAbove = $repository->getLog($repository->getRevision($against->getFullname().'..'.$branch->getFullname()));

            $rows[] = array(
                'branch'           => $branch,
                'above'            => $logAbove->count(),
                'behind'           => $logBehind->count(),
                'lastModification' => $branch->getLastModification(),
            );
        }

        usort($rows, function ($left, $right) {
            return $left['lastModification']->getAuthorDate() < $right['lastModification']->getAuthorDate();
        });

        return $rows;
    }

    public function getGravatar($email, $size = 50)
    {
        return 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$size;
    }

    public function getName()
    {
        return 'gitonomy';
    }
}
