<?php

namespace Gitonomy\Bundle\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ReceiveReferenceEvent;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\CoreBundle\Entity\Thread;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class ThreadListener
{
    protected $registry;
    protected $repositoryPool;

    public function __construct(Registry $registry, RepositoryPool $repositoryPool)
    {
        $this->registry       = $registry;
        $this->repositoryPool = $repositoryPool;
    }

    public function delete(ReceiveReferenceEvent $event)
    {
    }

    public function create(ReceiveReferenceEvent $event)
    {
        $em     = $this->registry->getEntityManager();
        $thread = new Thread(
            $event->getProject(),
            $event->getUser(),
            $event->getReference()
        );

        $em->persist($thread);
        $em->flush();

        $this->doWrite($thread, $event, ThreadMessage::TYPE_GIT_COMMIT);
    }

    public function write(ReceiveReferenceEvent $event)
    {
        $thread = $this->getThreadFromReference($event->getReference());

        $this->doWrite($thread, $event, ThreadMessage::TYPE_GIT_COMMIT);
    }

    protected function doWrite(Thread $thread, ReceiveReferenceEvent $event, $type, $message = '')
    {
        $em         = $this->registry->getEntityManager();
        $repository = $this->repositoryPool->getGitRepository($event->getProject());
        $log        = $event->getLog($repository);

        foreach ($log as $commit) {
            $message.= $commit->getShortHash().': '.$commit->getShortMessage()."\n";
        }

        $threadMessage = new ThreadMessage($thread, $event->getUser(), $type, $message);

        $em->persist($threadMessage);
        $em->flush();
    }

    protected function getThreadFromReference($reference)
    {
        $em = $this->registry->getEntityManager();
        $thread = $em->getRepository('GitonomyCoreBundle:Thread')->findOneByReference($reference);

        if (null === $thread) {
            throw new \RuntimeException(sprintf('No thread found with reference "%s"', $reference));
        }

        return $thread;
    }
}
