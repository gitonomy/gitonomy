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
use Symfony\Component\Console\Input\InputArgument;
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
            ->setHelp(<<<EOF
The <info>gitonomy:events:process</info> executes background events processing
and stop when all jobs are finished.
EOF
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dispatcher = $this->getContainer()->get('gitonomy_core.event_dispatcher');
        while (true) {
            $res = $dispatcher->runAsync();
            if ($res) {
                $output->write(".");
            } else {
                break;
            }
        }
        $output->writeln("");
    }
}
