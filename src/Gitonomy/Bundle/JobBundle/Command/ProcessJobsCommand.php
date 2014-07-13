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

namespace Gitonomy\Bundle\JobBundle\Command;

use Gitonomy\Bundle\CoreBundle\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessJobsCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('gitonomy:process-jobs')
            ->setDescription('Process background jobs')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $jobManager = $this->getContainer()->get('gitonomy.job_manager');

        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            $sigHandler = function () use ($jobManager, $output) {
                $output->writeln('<comment>- stopping...</comment>');
                $jobManager->stop();

                exit;
            };

            pcntl_signal(SIGQUIT, $sigHandler);
            pcntl_signal(SIGTERM, $sigHandler);
            pcntl_signal(SIGINT,  $sigHandler);
            pcntl_signal(SIGHUP,  $sigHandler);
            pcntl_signal(SIGUSR1, $sigHandler);
        }

        $jobManager->runBackground($output);
    }
}
