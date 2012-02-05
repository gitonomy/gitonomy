<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('permission', InputArgument::REQUIRED, 'Slug of the project')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'If it\'s a project permission, indicate the project')
            ->setDescription('Tests a permission and returns 0 if OK, 1 otherwise')
            ->setHelp(<<<EOF
The <info>gitonomy:permission-check</info> allows you to test if a user has a given permission/

You can specify a project with <info>--project</info> option.

<comment>Sample usages</comment>

  > php app/console gitonomy:permission-check --project=foo alice GIT_WRITE

    Tests if alice can write on Git repository of foo project.

EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em   = $this->getContainer()->get('doctrine')->getEntityManager();

        $projectSlug = $input->getOption('project');
        $project = null;
        if (null !== $projectSlug) {
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);
            if (null === $project) {
                throw new \RuntimeException(sprintf('Project with slug "%s" not found', $projectSlug));
            }
        }

        $username = $input->getArgument('username');
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (null === $username) {
            throw new \RuntimeException(sprintf('User "%s" not found', $username));
        }

        $permission = $input->getArgument('permission');
        $test = false;

        return $test ? 0 : 1;
    }
}
