<?php

namespace Gitonomy\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\Repository;
use Gitonomy\Bundle\CoreBundle\EventListener\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventListener\Event\ProjectCreateEvent;

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
            ->addOption('main-branch', null, InputOption::VALUE_OPTIONAL, 'The main branch of project')
            ->setDescription('Creates a new project and initializes its repository')
            ->setHelp(<<<EOF
The <info>gitonomy:project-create</info> command creates a project and initializes
it repository.

You can specify the main branch of project with <info>--main-branch</info> option.

<comment>Sample usages</comment>

  > php app/console gitonomy:project-create "My Project" my-project

    Creates a new project with name "<comment>My project</comment>" and slugged "<comment>my-project</comment>"

  > php app/console gitonomy:project-create --main-branch=develop "My Project" my-project

    Like above, but set the main branch to "<comment>develop</comment>" instead of "<comment>master</comment>"

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

        $project = new Project();
        $project->setName($input->getArgument('name'));
        $project->setSlug($input->getArgument('slug'));

        if ($mainBranch = $input->getOption('main-branch')) {
            $project->setMainBranch($mainBranch);
        }

        $em->persist($project);
        $em->flush();

        $event = new ProjectCreateEvent($project);
        $this->getContainer()->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, $event);

        $output->writeln(sprintf('Project <info>%s</info> was created!', $project->getName()));
    }
}
