<?php

namespace Gitonomy\Bundle\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\CoreBundle\Entity\Thread;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CloseMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CommitMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\ForceMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\OpenMessage;

class ThreadListener
{
    protected $registry;
    protected $repositoryPool;

    public function __construct(Registry $registry, RepositoryPool $repositoryPool)
    {
        $this->registry       = $registry;
        $this->repositoryPool = $repositoryPool;
    }

    public function onPush(PushReferenceEvent $event)
    {
        $em        = $this->registry->getEntityManager();
        $thread    = $this->getThread($event);
        $reference = $event->getReference();

        if ($reference->isCreate()) {
            $message = new OpenMessage($thread, $event->getUser());
            $em->persist($message);
            // used to save the open message before the commit message
            $em->flush();
        }

        $message = $this->getMessageFromEvent($event);

        $em->persist($message);
        $em->flush();
    }

    protected function getMessageFromEvent(PushReferenceEvent $event)
    {
        $thread    = $this->getThread($event);
        $user      = $event->getUser();
        $reference = $event->getReference();

        if ($reference->isDelete()) {
            return new CloseMessage($thread, $user);
        }

        $message = new CommitMessage($thread, $user);
        $message->setForce($reference->isForce());

        $log     = $event->getReference()->getLog();
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

        return $message;
    }

    protected function getThread(PushReferenceEvent $event)
    {
        $em   = $this->registry->getEntityManager();
        $repo = $em->getRepository('GitonomyCoreBundle:Thread');
        $project = $event->getProject();
        $reference = $event->getReference()->getReference();

        return $repo->findOneOrCreate($project, $reference);
    }
}
