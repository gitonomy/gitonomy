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

namespace Gitonomy\Bundle\CoreBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Bundle\CoreBundle\Entity\Feed;
use Gitonomy\Bundle\CoreBundle\Entity\Message;
use Gitonomy\Bundle\CoreBundle\Entity\Message\OpenMessage;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class FeedListener implements EventSubscriberInterface
{
    protected $registry;
    protected $repositoryPool;

    public function __construct(Registry $registry, RepositoryPool $repositoryPool)
    {
        $this->registry       = $registry;
        $this->repositoryPool = $repositoryPool;
    }

    public static function getSubscribedEvents()
    {
        return array(
            GitonomyEvents::PROJECT_PUSH => array(array('onProjectPush', -256)),
        );
    }

    public function onProjectPush(PushReferenceEvent $event)
    {
        $em        = $this->registry->getManager();
        $feed      = $this->getFeed($event);
        $reference = $event->getReference();

        if ($reference->isCreate()) {
            $message = new OpenMessage($feed, $event->getUser());
            $em->persist($message);
            // used to save the open message before the commit message
            $em->flush();
        }

        $message = $this->getMessageFromEvent($event, $feed);

        if ($message) {
            $em->persist($message);
            $em->flush();
        }
    }

    protected function getMessageFromEvent(PushReferenceEvent $event, Feed $feed)
    {
        return Message::createFromEvent($event, $feed);
    }

    protected function getFeed(PushReferenceEvent $event)
    {
        $em   = $this->registry->getManager();
        $repo = $em->getRepository('GitonomyCoreBundle:Feed');
        $project = $event->getProject();
        $reference = $event->getReference()->getReference();

        return $repo->findOneOrCreate($project, $reference);
    }
}
