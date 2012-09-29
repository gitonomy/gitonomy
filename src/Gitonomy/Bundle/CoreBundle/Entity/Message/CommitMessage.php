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

namespace Gitonomy\Bundle\CoreBundle\Entity\Message;

use Gitonomy\Bundle\CoreBundle\Entity\Message;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class CommitMessage extends Message
{
    protected $commitList;
    protected $commits;
    protected $commitCount;
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
        $this->commitList = json_encode($commits);

        return $this;
    }

    public function getCommitCount()
    {
        return $this->commitCount;
    }

    public function setCommitCount($commitCount)
    {
        $this->commitCount = $commitCount;

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
