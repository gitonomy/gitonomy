<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Revision;

interface GitUrlGeneratorInterface
{
    public function generateCommitUrl(Commit $commit);
    public function generateReferenceUrl(Reference $reference);
    public function generateTreeUrl(Revision $revision, $path = '');
}
