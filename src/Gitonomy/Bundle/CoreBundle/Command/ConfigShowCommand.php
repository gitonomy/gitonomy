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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configuration command for Gitonomy.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ConfigShowCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('gitonomy:config:show')
            ->addArgument('parameter', InputArgument::OPTIONAL, 'Display a single parameter value')
            ->setDescription('Shows configuration of the application')
            ->setHelp(<<<EOF
The <info>gitonomy:config:show</info> displays informations about the configuration of
the application.
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $config    = $container->get('gitonomy_core.config');

        $config = array(
            'name'            => $config->get('name'),
            'baseline'        => $config->get('baseline'),
            'ssh_access'      => $config->get('ssh_access'),
            'repository_path' => $container->getParameter('repository_path'),
        );

        $parameter = $input->getArgument('parameter');

        if (null === $parameter) {
            $max = array_reduce(array_keys($config), function ($result, $item) {
                return max($result, strlen($item));
            });

            foreach ($config as $name => $value) {
                $output->writeln(sprintf("%-${max}s  %s", $name, $value));
            }

        } elseif (!isset($config[$parameter])) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" is not present in configuration. Present are: "%s".', $parameter, implode(', ', array_keys($config))), 1);
        } else {
            $output->writeln($config[$parameter]);
        }
    }
}
