<?php

namespace Gitonomy\Bundle\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ReceiveReferenceEvent;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\CoreBundle\Entity\Thread;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CreateMessage;
use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage\CommitMessage;

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

        $threadMessage = new CreateMessage($thread, $event->getUser());

        $em->persist($thread);
        $em->persist($threadMessage);
        $em->flush();
    }

    public function write(ReceiveReferenceEvent $event)
    {
        $em         = $this->registry->getEntityManager();
        $thread     = $this->getThreadFromReference($event->getReference());
        $repository = $this->repositoryPool->getGitRepository($event->getProject());
        $log        = $event->getLog($repository);

        $message = '';
        foreach ($log as $commit) {
            $message.= $commit->getShortHash().': '.$commit->getShortMessage()."\n";
        }

        $threadMessage = new CommitMessage($thread, $event->getUser());

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
