<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Security\CliToken;
use Gitonomy\Bundle\CoreBundle\Git\PushTarget;

/**
 * Shell command for checking a permission.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class GitPermissionCheckCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:git-permission-check')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('project', InputArgument::REQUIRED, 'Project\'s slug')
            ->addArgument('permission', InputArgument::REQUIRED, 'Name of permission')
            ->addArgument('reference',  InputArgument::REQUIRED, 'Reference')
            ->setDescription('Tests a permission and returns 0 if OK, 1 otherwise')
            ->setHelp(<<<EOF
The <info>gitonomy:git-permission-check</info> tests git permission on a repository.

<comment>Sample usages</comment>

  > php app/console gitonomy:git-permission-check alice foobar GIT_DELETE refs/head/my-feature

EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $slug       = $input->getArgument('project');
        $permission = $input->getArgument('permission');
        $reference  = $input->getArgument('reference');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);
        if (null === $project) {
            throw new \RuntimeException(sprintf('Project with slug "%s" not found', $slug));
        }

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (null === $user) {
            throw new \RuntimeException(sprintf('User "%s" not found', $username));
        }

        $context = $this->getContainer()->get('security.context');
        $token = new CliToken($user->getRoles());
        $token->setUser($user);
        $token->setAuthenticated(true);

        $context->setToken($token);

        $test = $context->isGranted($permission, new PushTarget($project, $reference));

        return $test ? 0 : 1;
    }
}
