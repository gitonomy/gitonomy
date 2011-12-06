<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper for Git command.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 *
 * @todo Cleanup and refactor
 */
class GitCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:git')
            ->addArgument('username', InputArgument::REQUIRED, 'Name of the user executing Git')
            ->setDescription('Wraps the Git command')
            ->setHelp(<<<EOF
The <info>gitonomy:git</info> command wraps Git to add Gitonomy logic in it.
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute($input, $output);
        } catch (\Exception $e)
        {
            fputs(STDERR, $e->getMessage()."\n");
        }
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $originalCommand = $_SERVER['SSH_ORIGINAL_COMMAND'];

        $username = $input->getArgument('username');
        $user = $this->getContainer()->get('doctrine')
            ->getRepository('GitonomyCoreBundle:User')
            ->findOneByUsername($username)
        ;

        if (!$user) {
            throw new \RuntimeException('Sorry, seems the user your are using does not exists anymore');
        }

        if (!preg_match('#^(git-(receive|upload)-pack) \'([a-z]+)/([a-z]+).git\'#', $originalCommand, $vars)) {
            throw new \RuntimeException('Action seems illegal: '.$originalCommand);
        }

        $command   = $vars[1];
        $namespace = $vars[3];
        $name      = $vars[4];

        $isReading = $command == 'git-upload-pack';

        $pool = $this->getContainer()->get('gitonomy_core.git.repository_pool');

        if (!$pool->exists($namespace, $name)) {
            if (!$isReading && $username === $namespace) {
                $pool->create($user, $name);
            } else {
                throw new \RuntimeException('Repository not found');
            }
        }

        if (!$isReading && $username !== $namespace) {
            throw new \RuntimeException('You cannot write if it\'s not your repository');
        }

        $repository = $pool->find($namespace, $name);
        $pool->command($repository, $command);
    }
}
