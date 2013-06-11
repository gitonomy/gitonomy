<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Tag;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Revision;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This abstract generator relies on configured route names and arguments.
 *
 * You can inject the configuration through the constructor. To know about available names,
 * look at static method {getRouteNames} and {getRouteArgs}
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class AbstractGitUrlGenerator implements GitUrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var string[] associative array of route names.
     *
     * @see self::getRouteNames
     */
    private $routeNames;

    /**
     * @var string[] associative array of route argument names.
     *
     * @see self::getRouteArgs
     */
    private $routeArgs;

    /**
     * @inheritdoc
     */
    abstract protected function getName(Repository $repository);

    public function __construct(UrlGeneratorInterface $generator, array $routeNames = array(), array $routeArgs = array())
    {
        $this->generator  = $generator;
        $this->routeNames = array_merge(self::getRouteNames(), $routeNames);
        $this->routeArgs  = array_merge(self::getRouteArgs(), $routeArgs);
    }

    /**
     * @inheritdoc
     */
    public function generateCommitUrl(Commit $commit)
    {
        return $this->generator->generate($this->routeNames['commit'], array(
            $this->routeArgs['commit_repository'] => $this->getName($commit->getRepository()),
            $this->routeArgs['commit_hash']       => $commit->getHash()
        ));
    }

    /**
     * @inheritdoc
     */
    public function generateReferenceUrl(Reference $reference)
    {
        if ($reference instanceof Branch) {
            return $this->generator->generate($this->routeNames['branch'], array(
                $this->routeArgs['branch_repository'] => $this->getName($reference->getRepository()),
                $this->routeArgs['branch_name']       => $reference->getName(),
            ));
        }

        if ($reference instanceof Tag) {
            return $this->generator->generate($this->routeNames['tag'], array(
                $this->routeArgs['tag_repository'] => $this->getName($reference->getRepository()),
                $this->routeArgs['tag_name']       => $reference->getName(),
            ));
        }

        throw new \InvalidArgumentException(sprintf('Expected a Branch, got a "%s".', is_object($reference) ? get_class($reference) : gettype($reference)));
    }

    /**
     * @inheritdoc
     */
    public function generateTreeUrl(Revision $revision, $path = '')
    {
        if ($revision instanceof Tag or $revision instanceof Branch) {
            $rev = $revision->getName();
        } else {
            $rev = $revision->getRevision();
        }

        return $this->generator->generate($this->routeNames['tree'], array(
            $this->routeArgs['tree_repository'] => $this->getName($revision->getRepository()),
            $this->routeArgs['tree_revision']   => $rev,
            $this->routeArgs['tree_path']       => $path,
        ));
    }

    /**
     * Returns route names being used for URL generation.
     *
     * See sourcecode of this method for an exhaustive list.
     *
     * @return string[] associative array of route names
     */
    public static function getRouteNames()
    {
        return array(
            'commit' => 'commit',
            'branch' => 'branch',
            'tag'    => 'tag',
            'tree'   => 'tree'
        );
    }

    /**
     * Returns route argument names being used for URL generation.
     *
     * See sourcecode of this method for an exhaustive list.
     *
     * @return string[] associative array of route names
     */
    public static function getRouteArgs()
    {
        return array(
            'commit_repository' => 'repository',
            'commit_hash'       => 'hash',
            'branch_repository' => 'repository',
            'branch_name'       => 'name',
            'tag_repository'    => 'repository',
            'tag_name'          => 'name',
            'tree_repository'   => 'repository',
            'tree_revision'     => 'revision',
            'tree_path'         => 'path',
        );
    }
}
