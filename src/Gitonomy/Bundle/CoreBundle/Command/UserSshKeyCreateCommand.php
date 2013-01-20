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

/**
 * Command tool for adding a SSH key to a user.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class UserSshKeyCreateCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:user-ssh-key-create')
            ->addArgument('username', InputArgument::REQUIRED, 'A username')
            ->addArgument('title',    InputArgument::REQUIRED, 'Title of the key')
            ->addArgument('content',  InputArgument::REQUIRED, 'The SSH-key content')
            ->setDescription('Adds a SSH key to a user')
            ->setHelp(<<<EOF
The <info>gitonomy:user-ssh-key-create</info> adds a key to an existing user.

<comment>Sample usage:</comment>

  > php app/console gitonomy:user-ssh-key-create alice "Desktop" "ssh-rsa ..."
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $title    = $input->getArgument('title');
        $content  = $input->getArgument('content');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw new \RuntimeException(sprintf('User with username "%s" not found', $username));
        }

        $key = $user->createSshKey($title, $content);

        $em->persist($key);
        $em->flush();

        $output->writeln(sprintf("The key named <info>%s</info> was successfully added to user <info>%s</info>!", $title, $user->getUsername()));
    }
}
