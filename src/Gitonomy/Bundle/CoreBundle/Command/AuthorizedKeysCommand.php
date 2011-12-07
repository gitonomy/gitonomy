<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shell command for generating the authorized_keys file.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AuthorizedKeysCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:authorized-keys')
            ->addOption('mark-as-installed', 'i', InputOption::VALUE_NONE, 'Mark all the keys as installed')
            ->setDescription('Generates the authorized_keys file')
            ->setHelp(<<<EOF
The <info>gitonomy:authorized-keys</info> generates the file for authentication
of users via SSH.

Use the option <info>-i</option> to mark all keys as installed correctly.
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $authorizedKeys = $this->getContainer()
            ->get('gitonomy_core.git.authorized_keys_generator')
            ->generate($input->getOption('mark-as-installed'))
        ;

        $output->write($authorizedKeys);
    }
}
