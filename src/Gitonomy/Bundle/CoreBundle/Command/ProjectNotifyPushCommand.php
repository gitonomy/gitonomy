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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;

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
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('project', InputArgument::REQUIRED, 'Project slug')
            ->setDescription('Notification after a push in a project')
            ->setHelp(<<<EOF
The <info>gitonomy:project-notify-push</info> is used to notify the application after a push.

<comment>Sample usages</comment>

  > php app/console gitonomy:project-notify-push alice foobar

    Notify that alice pushed to the repository

EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em   = $this->getContainer()->get('doctrine')->getEntityManager();

        $projectSlug = $input->getArgument('project');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);
        if (null === $project) {
            throw new \RuntimeException(sprintf('Project with slug "%s" not found', $projectSlug));
        }

        $username = $input->getArgument('username');
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (null === $username) {
            throw new \RuntimeException(sprintf('User "%s" not found', $username));
        }

        $event = new ProjectEvent($project, $user);
        $this->getContainer()->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_PUSH, $event);

        $em->persist($project);
        $em->flush();
    }
}
