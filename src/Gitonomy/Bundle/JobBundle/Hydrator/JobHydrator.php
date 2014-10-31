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

namespace Gitonomy\Bundle\JobBundle\Hydrator;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JobHydrator
{
    private $container;
    private $jobClasses;

    public function __construct(ContainerInterface $container, array $jobClasses = array())
    {
        $this->container  = $container;
        $this->jobClasses = $jobClasses;
    }

    public function getName($class)
    {
        foreach ($this->jobClasses as $name => $current) {
            if ($class == $current) {
                return $name;
            }
        }

        throw new \InvalidArgumentException(sprintf('Job class "%s" is not registered.', $class));
    }

    public function hydrateJob($name, array $parameters = array())
    {
        if (!isset($this->jobClasses[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'No job of type "%s". Available are: %s',
                $name,
                implode(', ', array_keys($this->jobClasses))
            ));
        }

        $class = $this->jobClasses[$name];

        $job = new $class($parameters);

        if ($job instanceof ContainerAwareInterface) {
            $job->setContainer($this->container);
        }

        return $job;
    }
}
