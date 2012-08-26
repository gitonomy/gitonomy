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
        $em = $this->registry->getEntityManager();
        $thread = new Thread(
            $event->getProject(),
            $event->getUser(),
            $event->getReference()
        );

        $em->persist($thread);
        $em->flush();

        $this->doWrite($thread, $event);
    }

    public function write(ReceiveReferenceEvent $event)
    {
        $em = $this->registry->getEntityManager();
        $thread = $em->getRepository('GitonomyCoreBundle:Thread')->findOneByReference($event->getReference());

        $this->doWrite($thread, $event);
    }

    protected function doWrite(Thread $thread, ReceiveReferenceEvent $event)
    {
        $em = $this->registry->getEntityManager();

        $threadMessage = new ThreadMessage(
            $thread,
            $event->getUser(),
            $event->getUser() . ' pushed commit '.$event->getAfter()
        );

        $em->persist($threadMessage);
        $em->flush();
    }
}
