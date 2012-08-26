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

namespace Gitonomy\Bundle\CoreBundle\Command;

use Gitonomy\Git\ReceiveReference;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ReceiveReferenceEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectNotifyPushCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:project-notify-push')
            ->addArgument('project', InputArgument::REQUIRED, 'Project slug')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('request', InputArgument::REQUIRED, 'Request')
            ->addArgument('reference', InputArgument::REQUIRED, 'Reference')
            ->addArgument('before', InputArgument::REQUIRED, 'Before')
            ->addArgument('after', InputArgument::REQUIRED, 'After')
            ->setDescription('Notification after a push in a project')
            ->setHelp(<<<EOF
The <info>gitonomy:project-notify-push</info> is used to notify the application after a push.

<comment>Sample usages</comment>

  > php app/console gitonomy:project-notify-push alice foobar commitBefore commitAfter reference

    Notify that alice pushed to the repository

EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
        throw new \Exception('bla');
            $this->doExecute($input, $output);

            return 0;
        } catch (\Exception $e) {
            throw $e;
            return 1;
        }
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $projectSlug = $input->getArgument('project');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);
        if (null === $project) {
            throw new \RuntimeException(sprintf('Project with slug "%s" not found', $projectSlug));
        }

        $username = $input->getArgument('username');
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (null === $user) {
            throw new \RuntimeException(sprintf('User "%s" not found', $username));
        }

        $request   = $input->getArgument('request');
        $reference = $input->getArgument('reference');
        $before    = $input->getArgument('before');
        $after     = $input->getArgument('after');

        $event = new ReceiveReferenceEvent($project, $user, $reference, $before, $after);
        $this->dispatch($event, $request);
    }

    protected function dispatch(ReceiveReferenceEvent $event, $request)
    {
        $eventName = GitonomyEvents::getEventFromRequest($request);
        throw new \Exception($eventName);
        $this->getContainer()->get('event_dispatcher')->dispatch($eventName, $event);
    }
}
