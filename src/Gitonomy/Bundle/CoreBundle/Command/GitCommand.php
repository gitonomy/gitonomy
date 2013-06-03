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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Security\CliToken;

/**
 * Wrapper for Git command.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
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

It is meant to be called with environment variable SSH_ORIGINAL_COMMAND. It's
configured via the <info>authorized_keys</info> file, containing lines like:

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
        $securityContext = $this->getContainer()->get('security.context');

        $token = new CliToken($user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);
        $securityContext->setToken($token);

        $shellHandler = $this->getContainer()->get('gitonomy_core.git.shell_handler');
        $originalCommand = $shellHandler->getOriginalCommand();

        if (null === $originalCommand) {
            $this->outputUserInformation($output, $user);

            return;
        }

        if (!preg_match('#^(.*) \'(/?'.Project::SLUG_PATTERN.').git\'#', $originalCommand, $vars)) {
            throw new \RuntimeException('Command seems illegal: '.$originalCommand);
        }

        $action = $vars[1];
        $project = $this->getProject($vars[2]);

        switch ($action) {
            case 'git-receive-pack':
            case 'git-upload-pack':
                if (!$securityContext->isGranted('PROJECT_READ', $project)) {
                    throw new \RuntimeException('You are not allowed to read on this repository');
                }
                break;

            default:
                throw new \RuntimeException('Action seems illegal: '.$action);
        }

        $this->getContainer()->get('gitonomy_core.git.shell_handler')->handle($project, $action, array(
            'GITONOMY_PROJECT' => $project->getSlug(),
            'GITONOMY_USER'    => $user->getUsername(),
        ));
    }

    protected function outputUserInformation(OutputInterface $output, User $user)
    {
        $output->writeln("");
        $output->writeln("You are identified as ".$user->getUsername());
        $output->writeln("");

        $projectRoles = $user->getProjectRoles();

        $output->writeln(sprintf("   %-32s %-32s", "Project", "Your role"));
        $output->writeln(sprintf("   %-32s %-32s", "-------", "---------"));

        foreach ($projectRoles as $projectRole) {
            $projectName = $projectRole->getProject()->getName();
            $projectSlug = $projectRole->getProject()->getSlug();
            $roleName    = $projectRole->getRole()->getName();
            $output->writeln(sprintf("   %-32s %-32s", $projectName, $roleName));
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
