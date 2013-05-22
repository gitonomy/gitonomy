<?php

namespace Gitonomy\Bundle\TwigBundle\Routing;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;

interface GitUrlGeneratorInterface
{
    public function generateCommitUrl(Commit $commit);
    public function generateReferenceUrl(Reference $reference);
}
