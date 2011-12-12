<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\UserRole;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class UserAddToProjectCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:user-add-to-project')
            ->addArgument('username',     InputArgument::REQUIRED, 'Username')
            ->addArgument('project',      InputArgument::REQUIRED, 'Slug of the project')
            ->addArgument('role',         InputArgument::OPTIONAL, 'Role title to set')
            ->setDescription('Creates a new user')
            ->setHelp(<<<EOF
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $username = $input->getArgument('username');
        $projectSlug = $input->getArgument('project');

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw new \RuntimeException(sprintf('No user with username "%s"', $username));
        }

        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($projectSlug);
        if (!$project) {
            throw new \RuntimeException(sprintf('No project with slug "%s"', $projectSlug));
        }

        $roleName = $input->getArgument('role');
        if (!$roleName && !$input->isInteractive()) {
            throw new \RuntimeException('No role set');
        }
        if ($roleName) {
            $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName($roleName);
            if (!$role) {
                throw new \RuntimeException(sprintf('No role with name "%s"', $roleName));
            }
        } else {
            $roles = $em->getRepository('GitonomyCoreBundle:Role')->findBy(array('isGlobal' => false));
            $output->writeln("Select a role:");
            $i = 0;
            while ($i == 0) {
                foreach ($roles as $i => $role) {
                    $output->writeln(sprintf("  > <info>%s</info> %s", $i + 1, $role->getName()));
                }
                $dialog = $this->getDialogHelper();
                $input = (int) $dialog->ask($output, 'Which one? ');
                if ($input > count($roles)) {
                    $i = 0;
                }
            }
            $role = $roles[$input - 1];
        }

        $output->writeln(sprintf(
            'Adding <info>%s</info> to project <info>%s</info> with role <info>%s</info>',
            $user->getFullname(),
            $project->getName(),
            $role->getName()
        ));

        $userRole = new UserRole();
        $userRole->setUser($user);
        $userRole->setProject($project);
        $userRole->setRole($role);

        $em->persist($userRole);
        $em->flush();
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}
