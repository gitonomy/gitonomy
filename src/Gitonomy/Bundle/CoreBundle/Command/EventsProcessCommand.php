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

use Gitonomy\Git\PushReference;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class EventsProcessCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:events:process')
            ->addOption('wait',   'w', InputOption::VALUE_NONE, 'wait mode')
            ->addOption('max',    'm', InputOption::VALUE_OPTIONAL, 'max number of iterations')
            ->addOption('period', 'p', InputOption::VALUE_OPTIONAL, 'set the polling period in seconds', 1)
            ->setHelp(<<<EOF
The <info>gitonomy:events:process</info> executes background events processing
and stop when all jobs are finished.

You can pass '--wait' (-w) to set processor in wait mode. In this mode,
processor will wait 1 second before retrying to find an event to process:

    <info>php app/console gitonomy:events:process --wait</info>

The '--max' option permit to set number of maximum events to process. Due to
PHP limitations regarding garbage collecting, and to ease confort of the
application, you can restart this process with a daemonizer tool:

    <info>php app/console gitonomy:events:process --wait --max=100</info>

You also can use the 'period' option to specify the period of seconds this
process should wait before polling for an new event:

    <info>php app/console gitonomy:events:process --wait --max=100 --period=6</info>

EOF
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $wait   = $input->getOption('wait');
        $max    = $input->getOption('max');
        $period = $input->getOption('period');

        $dispatcher = $this->getContainer()->get('gitonomy_core.event_dispatcher');

        while (null === $max || $max > 0) {
            $res = $dispatcher->runAsync();
            if ($wait && !$res) {
                sleep($period);
            } elseif (!$wait && !$res) {
                break;
            }
            $max = null === $max ? null : $max - 1;
        }
    }
}
