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

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;
use Gitonomy\Bundle\CoreBundle\Entity\Role;

/**
 * Manage git accesses in projects.
 *
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class GitAccessCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:git-access')
            ->addArgument('verb', InputArgument::REQUIRED, 'The action to execute (ex: create)')
            ->addArgument('project', InputArgument::REQUIRED, 'The project')
            ->addArgument('role', InputArgument::REQUIRED, 'The role (ex: visitor, dev, lead-dev, admin)')
            ->addArgument('reference', InputArgument::REQUIRED, 'The git reference')
            ->addArgument('isWrite', null, InputArgument::OPTIONAL, 'Is writable?')
            ->addArgument('isAdmin', null, InputArgument::OPTIONAL, 'Is admin?')
            ->addOption('stderr', null, InputOption::VALUE_OPTIONAL, 'Use stderr for errors ?', true)
            ->setHelp(<<<EOF
The <info>gitonomy:git-access</info> command manages the git accesses.

<info>php app/console gitonomy:git-access create foobar lead-dev * 1 1 1</info>
EOF
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute($input, $output);

            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }
    /**
     * @inheritdoc
     */
    public function doExecute(InputInterface $input, OutputInterface $output)
    {
        $verb      = $input->getArgument('verb');
        $project   = $this->getProject($input->getArgument('project'));
        $role      = $this->getRole($input->getArgument('role'));
        $reference = $input->getArgument('reference');
        $em        = $this->getContainer()->get('doctrine')->getManager();

        if ('create' === $verb) {
            $isWrite = $input->getArgument('isWrite');
            $isAdmin = $input->getArgument('isAdmin');
            $access  = new ProjectGitAccess($project, $role, $reference, $isWrite, $isAdmin);

            $em->persist($access);
            $em->flush();

            $output->writeln('The git-access was successfully created!');

            return;
        }

        if ('delete' === $verb) {
            $access = $this->getGitAccess($project, $role, $reference);

            $em->remove($access);
            $em->flush();

            $output->writeln('The git-access was successfully deleted!');

            return;
        }

        throw new \RuntimeException(sprintf('Action "%s" not defined', $verb));
    }

    protected function getGitAccess(Project $project, Role $role, $reference)
    {
        $doctrine   = $this->getContainer()->get('doctrine');
        $repository = $doctrine->getRepository('GitonomyCoreBundle:ProjectGitAccess');
        $gitAccess  = $repository->findOneBy(array(
            'project'   => $project,
            'role'      => $role,
            'reference' => $reference,
        ));

        if (null === $gitAccess) {
            throw new \RuntimeException('GitAccess not found');
        }

        return $gitAccess;
    }
}
