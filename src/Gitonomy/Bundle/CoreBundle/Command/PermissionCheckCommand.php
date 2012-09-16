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
class PermissionCheckCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:permission-check')
            ->addOption('project', null, InputOption::VALUE_OPTIONAL, 'If it\'s a project permission, indicate the project')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('permission', InputArgument::REQUIRED, 'Name of permission')
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            return $this->doExecute($input, $output);
        } catch (\Exception $e) {
            return 1;
        }
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $project    = $this->getProject($input->getArgument('project'));
        $user       = $this->getProject($input->getArgument('username'));
        $permission = $input->getArgument('permission');

        $context = $this->getContainer()->get('security.context');
        $token   = new CliToken($user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        $context->setToken($token);
        $test = $context->isGranted($permission, $project);

        return $test ? 0 : 1;
    }
}
