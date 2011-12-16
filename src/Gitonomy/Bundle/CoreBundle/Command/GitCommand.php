<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * Wrapper for Git command.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 *
 * @todo This class is too critical to be tested (STDIN, STDOUT, STDERR as pipes).
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
            ->addArgument('username', InputArgument::REQUIRED, 'Authenticated user')
            ->setDescription('Wraps the Git command to ensure authorization')
            ->setHelp(<<<EOF
The <info>gitonomy:git</info> command wraps Git to check the authorizations of
the user passed as argument.

This commands only works properly is called via SSH. It's configured via the
<info>authorized_keys</info> file, containing lines like:

  > <comment>command="php app/console gitonomy:git alex" <SSH-KEY></comment>

It does not manage the <info>authentication</info>, but only the <info>authorization</info>
of the user.
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute($input, $output);
        } catch (\Exception $e) {
            fputs(STDERR, sprintf("An error occurred during execution:\n\n  %s\n\n", $e->getMessage()));
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

        if (!preg_match('#^(git-(receive|upload)-pack) \'('.Project::SLUG_PATTERN.').git\'#', $originalCommand, $vars)) {
            throw new \RuntimeException('Action seems illegal: '.$originalCommand);
        }

        $command     = $vars[1];
        $projectSlug = $vars[3];

        $isReading = $command == 'git-upload-pack';

        $project = $this->getContainer()->get('doctrine')
            ->getRepository('GitonomyCoreBundle:Project')
            ->findOneBySlug($projectSlug)
        ;

        if (!$project) {
            throw new \RuntimeException(sprintf('No project with slug "%s" found', $projectSlug));
        }

        $pool = $this->getContainer()->get('gitonomy_core.git.repository_pool');

        $pool->getGitRepository($project)->shell($command);
    }
}
