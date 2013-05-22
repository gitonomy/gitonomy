<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;

abstract class AbstractGitUrlGenerator implements GitUrlGeneratorInterface
{
    private $generator;
    private $routeNames;
    private $repositoryKey;

    abstract protected function getName(Repository $repository);

    public function __construct(UrlGeneratorInterface $generator, array $routeNames, $repositoryKey = 'repository')
    {
        foreach (array('commit', 'reference') as $routeName) {
            if (!isset($routeNames[$routeName])) {
                throw new \InvalidArgumentException(sprintf('Route named "%s" is not present.', $routeName));
            }
        }

        $this->generator     = $generator;
        $this->routeNames    = $routeNames;
        $this->repositoryKey = $repositoryKey;
    }

    public function generateCommitUrl(Commit $commit)
    {
        return $this->generator->generate($this->routeNames['commit'], array(
            $this->repositoryKey => $this->getName($commit->getRepository),
            'hash'               => $commit->getHash()
        ));
    }

    public function generateReferenceUrl(Reference $reference)
    {
        return $this->generator->generate($this->routeNames['reference'], array(
            $this->repositoryKey => $this->getName($commit->getRepository),
            'reference'          => $reference->getFullname(),
        ));
    }
}
