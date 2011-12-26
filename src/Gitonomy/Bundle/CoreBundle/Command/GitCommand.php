<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

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
            ->addOption('stderr', null, InputOption::VALUE_OPTIONAL, 'Use stderr for errors ?', true)
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
            if (true === $input->getOption('stderr')) {
                fputs(STDERR, sprintf("An error occurred during execution:\n\n  %s\n\n", $e->getMessage()));
            } else {
                throw $e;
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getUser($input->getArgument('username'));
        $right = $this->getContainer()->get('gitonomy_frontend.security.right');

        $shellHandler = $this->getContainer()->get('gitonomy_core.git.shell_handler');
        $originalCommand = $shellHandler->getOriginalCommand();

        if (null === $originalCommand) {
            $this->outputUserInformations($output, $user);

            return;
        }

        if (!preg_match('#^(.*) \'('.Project::SLUG_PATTERN.').git\'#', $originalCommand, $vars)) {
            throw new \RuntimeException('Command seems illegal: '.$originalCommand);
        }

        $action = $vars[1];
        $project = $this->getProject($vars[2]);

        switch ($action) {
            case 'git-receive-pack':
                if (!$right->isGrantedForProject($user, $project, 'GIT_READ')) {
                    throw new \RuntimeException('You are not allowed');
                }
                break;

            case 'git-upload-pack':
                if (!$right->isGrantedForProject($user, $project, 'GIT_WRITE')) {
                    throw new \RuntimeException('You are not allowed');
                }
                break;

            default:
                throw new \RuntimeException('Action seems illegal: '.$action);
        }

        $this->getContainer()->get('gitonomy_core.git.shell_handler')->handle($project, $action, array(
            'gitonomy_project' => $project->getSlug(),
            'gitonomy_user'    => $user->getUsername(),
        ));
    }

    protected function outputUserInformations(OutputInterface $output, User $user)
    {
        $projectName     = $this->getContainer()->getParameter('gitonomy_frontend.project.name');
        $projectBaseline = $this->getContainer()->getParameter('gitonomy_frontend.project.baseline');
        $sshAccess       = $this->getContainer()->getParameter('gitonomy_frontend.ssh_access');

        $output->writeln(sprintf("<info>%s</info> - %s", $projectName, $projectBaseline));
        $output->writeln("");
        $output->writeln("You are identified as ".$user->getUsername());
        $output->writeln("");

        $projectRoles = $user->getProjectRoles();

        $output->writeln(sprintf("   %-32s %-32s %s", "Project", "Your role", "Checkout URL"));
        $output->writeln(sprintf("   %-32s %-32s %s", "-------", "---------", "------------"));

        foreach ($projectRoles as $projectRole) {
            $projectName = $projectRole->getProject()->getName();
            $projectSlug = $projectRole->getProject()->getSlug();
            $projectUrl  = $sshAccess.':'.$projectSlug.'.git';
            $roleName    = $projectRole->getRole()->getName();
            $output->writeln(sprintf("   %-32s %-32s %s", $projectName, $roleName, $projectUrl));
        }

        $output->writeln("");
    }

    /**
     * Returns a user by username.
     *
     * @param string $username A username
     *
     * @throws \RuntimeException User does not exists
     */
    protected function getUser($username)
    {
        $user = $this->getContainer()->get('doctrine')
            ->getRepository('GitonomyCoreBundle:User')
            ->findOneByUsername($username)
        ;

        if (null === $user) {
            throw new \RuntimeException('Sorry, seems the user your are using does not exists anymore');
        }

        return $user;
    }

    /**
     * Returns a project by slug.
     *
     * @param string $slug A project slug
     *
     * @throws \RuntimeException Project does not exists
     */
    protected function getProject($slug)
    {
        $project = $this->getContainer()->get('doctrine')
            ->getRepository('GitonomyCoreBundle:Project')
            ->findOneBySlug($slug)
        ;

        if (!$project) {
            throw new \RuntimeException(sprintf('No project with slug "%s" found', $slug));
        }

        return $project;
    }
}
