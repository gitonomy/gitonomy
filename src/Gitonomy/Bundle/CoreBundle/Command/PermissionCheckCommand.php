<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Security\CliToken;

/**
 * Shell command for checking a permission.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class PermissionCheckCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:permission-check')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('permission', InputArgument::REQUIRED, 'Name of permission')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'If it\'s a project permission, indicate the project')
            ->setDescription('Tests a permission and returns 0 if OK, 1 otherwise')
            ->setHelp(<<<EOF
The <info>gitonomy:permission-check</info> allows you to test if a user has a given permission/

You can specify a project with <info>--project</info> option.

<comment>Sample usages</comment>

  > php app/console gitonomy:permission-check --project=foo alice PROJECT_CONTRIBUTE

    Tests if alice is contributor of project foo

EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $permission = $input->getArgument('permission');
        $em   = $this->getContainer()->get('doctrine')->getEntityManager();

        $projectSlug = $input->getOption('project');
        $project = null;
        if ($projectSlug) {
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);
            if (null === $project) {
                throw new \RuntimeException(sprintf('Project with slug "%s" not found', $projectSlug));
            }
        }

        $username = $input->getArgument('username');
        $user = null;
        if ($username) {
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            if (null === $user) {
                throw new \RuntimeException(sprintf('User "%s" not found', $username));
            }
        }

        $context = $this->getContainer()->get('security.context');
        $token = new CliToken($user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        $context->setToken($token);
        $test = $context->isGranted($permission, $project);

        return $test ? 0 : 1;
    }
}
