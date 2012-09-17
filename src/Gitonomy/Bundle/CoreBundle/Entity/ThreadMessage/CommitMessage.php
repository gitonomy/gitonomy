<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class CommitMessage extends ThreadMessage
{
    protected $commitList;

    public function getSentence()
    {
        return 'pushed';
    }

    public function getCommitList()
    {
        return $this->commitList;
    }

    public function setCommitList(array $commitList)
    {
        $this->commitList = $commitList;

        return $this;
    }

    public function getName()
    {
        return 'commit';
    }
}
