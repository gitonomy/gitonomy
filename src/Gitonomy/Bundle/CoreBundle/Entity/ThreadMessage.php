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

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CloseMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CommitMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\ForceMessage;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class ThreadMessage
{
    protected $id;
    protected $thread;
    protected $user;
    protected $type;
    protected $publishedAt;

    public static function createFromEvent(PushReferenceEvent $event, Thread $thread)
    {
        $user      = $event->getUser();
        $reference = $event->getReference();

        if ($reference->isDelete()) {
            return new CloseMessage($thread, $user);
        }

        $message = new CommitMessage($thread, $user);
        $message->setForce($reference->isForce());

        $log     = $event->getReference()->getLog();
        $log->setLimit(5);
        $commits = array();
        foreach ($log as $commit) {
            array_push($commits, array(
                'hash'         => $commit->getHash(),
                'message'      => $commit->getMessage(),
                'shortMessage' => $commit->getShortMessage(),
                'authorName'   => $commit->getAuthorName(),
                'authorEmail'  => $commit->getAuthorEmail(),
            ));
        }

        $message->setCommits($commits);

        $message->setCommitCount(count($log));

        return $message;
    }

    public function __construct(Thread $thread, User $user, \DateTime $publishedAt = null)
    {
        $this
            ->setThread($thread)
            ->setUser($user)
            ->setPublishedAt($publishedAt)
        ;
    }

    public function getId()
    {
        return $id;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt = null)
    {
        $this->publishedAt = $publishedAt ? $publishedAt : new \DateTime();

        return $this;
    }
}
