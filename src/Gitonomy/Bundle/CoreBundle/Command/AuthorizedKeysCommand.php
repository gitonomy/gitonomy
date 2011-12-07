<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Git\AuthorizedKeysGenerator;

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
        $markAsInstalled = $input->getOption('mark-as-installed');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $shellCommand = $this->getContainer()->getParameter('gitonomy_core.git.shell_command');
        $keyList = $em->transactional(function ($em) use ($markAsInstalled) {
            $repository = $em->getRepository('GitonomyCoreBundle:UserSshKey');
            $keyList = $repository->getKeyList();
            if ($markAsInstalled) {
                $repository->markAllAsInstalled();
            }

            return $keyList;
        });

        // Here we test true, because $em->transactional returns true if the list was an empty list
        if (empty($keyList) || true === $keyList) {
            throw new \LogicException('Cannot generate the authorized_keys file: no key in database');
        }

        $generator = new AuthorizedKeysGenerator();

        $output->write($generator->generate($keyList, $shellCommand));
    }
}
