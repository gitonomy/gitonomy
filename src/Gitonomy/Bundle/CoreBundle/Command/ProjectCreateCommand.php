<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\Repository;

/**
 * Shell command for creating a project.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectCreateCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:project-create')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the project')
            ->addArgument('slug', InputArgument::REQUIRED, 'Slug of the project')
            ->setDescription('Creates a new project and initializes its repository')
            ->setHelp(<<<EOF
The <info>gitonomy:project-create</info> command creates a project and initializes
it repository.

<comment>Sample usages</comment>

  > php app/console gitonomy:project-create "My Project" my-project

    Creates a new project with name "<comment>My project</comment>" and slugged "<comment>my-project</comment>"
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
        $pool = $this->getContainer()->get('gitonomy_core.git.repository_pool');

        $project = new Project();
        $project->setName($input->getArgument('name'));
        $project->setSlug($input->getArgument('slug'));

        $repository = new Repository();
        $repository->setProject($project);

        $em->persist($project);
        $em->persist($repository);
        $em->flush();

        $pool->create($repository);

        $output->writeln(sprintf('Project <info>%s</info> was created!', $project->getName()));
    }
}
