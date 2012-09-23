<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\Thread;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class CommitMessage extends ThreadMessage
{
    protected $commitList;
    protected $commits;
    protected $isForce = false;

    public function getCommits()
    {
        if (null === $this->commits) {
            $commits = stream_get_contents($this->commitList);

            $this->commits = json_decode($commits, true);
        }

        return $this->commits;
    }

    public function setCommits(array $commits)
    {
        $this->setCommitList(json_encode($commits));

        return $this;
    }

    public function getCommitList()
    {
        return $this->commitList;
    }

    public function setCommitList($commitList)
    {
        $this->commitList = $commitList;

        return $this;
    }

    public function setForce($force)
    {
        $this->isForce = (bool) $force;
    }

    public function isForce()
    {
        return $this->isForce;
    }

    public function getName()
    {
        return 'commit';
    }
}
