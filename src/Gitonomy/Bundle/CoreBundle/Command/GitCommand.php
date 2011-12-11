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

        if (!preg_match('#^(git-(receive|upload)-pack) \'([a-z]+)(/([a-z]+))?.git\'#', $originalCommand, $vars)) {
            throw new \RuntimeException('Action seems illegal: '.$originalCommand);
        }

        $command   = $vars[1];
        $projectSlug = $vars[3];
        $username    = isset($vars[5]) ? $vars[5] : null;

        $isReading = $command == 'git-upload-pack';

        $project = $this->getContainer()->get('doctrine')
            ->getRepository('GitonomyCoreBundle:Project')
            ->findOneBySlug($projectSlug)
        ;

        if (! $project) {
            throw new \RuntimeException(sprintf('No project with slug "%s" found', $projectSlug));
        }

        if (null === $username) {
            $repository = $project->getMainRepository();
        } else {
            $repository = $project->getUserRepository($username);

            if (null === $repository) {
                throw new \RuntimeException(sprintf('The repository "%s" wat not found for user "%s"', $projectSlug, $username));
            }
        }

        $pool = $this->getContainer()->get('gitonomy_core.git.repository_pool');

        $pool->getGitRepository($repository)->shell($command);
    }
}
