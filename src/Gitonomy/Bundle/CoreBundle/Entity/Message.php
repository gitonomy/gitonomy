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

use Gitonomy\Bundle\CoreBundle\Entity\Message\CloseMessage;
use Gitonomy\Bundle\CoreBundle\Entity\Message\CommitMessage;
use Gitonomy\Bundle\CoreBundle\Entity\Message\ForceMessage;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class Message
{
    protected $id;
    protected $feed;
    protected $user;
    protected $type;
    protected $publishedAt;

    public static function createFromEvent(PushReferenceEvent $event, Feed $feed)
    {
        $user      = $event->getUser();
        $reference = $event->getReference();

        if ($reference->isDelete()) {
            return new CloseMessage($feed, $user);
        }

        $message = new CommitMessage($feed, $user);
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

    public function __construct(Feed $feed, User $user, \DateTime $publishedAt = null)
    {
        $this
            ->setFeed($feed)
            ->setUser($user)
            ->setPublishedAt($publishedAt)
        ;
    }

    public function getId()
    {
        return $id;
    }

    public function getFeed()
    {
        return $this->feed;
    }

    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;

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
