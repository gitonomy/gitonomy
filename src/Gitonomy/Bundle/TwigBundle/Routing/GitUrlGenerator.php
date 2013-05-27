<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Gitonomy\Git\Repository;

class GitUrlGenerator extends AbstractGitUrlGenerator
{
    public function getName(Repository $repository)
    {
        $name = basename($repository->getGitDir());

        if (preg_match('/^(.*)\.git$/', $name, $vars)) {
            return $vars[1];
        }

        return $name;
    }
}
