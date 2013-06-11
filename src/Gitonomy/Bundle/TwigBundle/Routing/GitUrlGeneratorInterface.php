<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Revision;

/**
 * URL generator required by twig extension to generate links over rendered
 * elements.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface GitUrlGeneratorInterface
{
    /**
     * Generates an URL for a given commit.
     *
     * @param Commit $commit
     */
    public function generateCommitUrl(Commit $commit);

    /**
     * Generates an URL for a reference.
     *
     * Can be a branch, a tag, a remote reference or any other possible
     * git reference.
     *
     * @param Reference $reference
     */
    public function generateReferenceUrl(Reference $reference);

    /**
     * Generates an URL for tree navigation (files and folders).
     *
     * @param Revision $revision the revision at which source is being browsed
     * @param string   $path     relative path to folder or file
     */
    public function generateTreeUrl(Revision $revision, $path = '');
}
