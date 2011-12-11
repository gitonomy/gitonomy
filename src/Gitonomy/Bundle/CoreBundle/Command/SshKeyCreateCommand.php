<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Git\AuthorizedKeysGenerator;
use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;

/**
 * Command tool for adding a SSH key to a given user.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SshKeyCreateCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:ssh-key-create')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to add key to')
            ->addArgument('title',    InputArgument::REQUIRED, 'Title of the key')
            ->addArgument('content',  InputArgument::REQUIRED, 'The SSH-key content')
            ->setDescription('Adds a SSH key to a user')
            ->setHelp(<<<EOF
The <info>gitonomy:ssh-key-add</info> adds a key to a user.

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

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw new \RuntimeException(sprintf('User with username "%s" not found', $username));
        }

        $sshKey = new UserSshKey();
        $sshKey->setUser($user);
        $sshKey->setTitle($title);
        $sshKey->setContent($content);

        $em->persist($sshKey);
        $em->flush();
    }
}
