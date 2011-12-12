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
            ->addOption('mark-as-installed', 'i', InputOption::VALUE_NONE, 'Mark all the keys as installed in database')
            ->setDescription('Generates the authorized_keys file')
            ->setHelp(<<<EOF
The <info>gitonomy:authorized-keys</info> generates the file for authentication
of users via SSH.

The option <info>-i</info> marks all keys as installed correctly.

<comment>Sample usages</comment>:

  > php app/console gitonomy:authorized-keys

      Generates all the authorized_keys file but do not update database to
      indicate that keys are installed on machine.

  > php app/console gitonomy:authorized-keys -i

      Same output as above, but updates database to mark them all as installed.

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
