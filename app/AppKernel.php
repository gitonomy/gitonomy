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

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    const VERSION = '0.5-DEV';

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),

            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Buzz\Bundle\BuzzBundle\BuzzBundle(),

            new Gitonomy\Bundle\CoreBundle\GitonomyCoreBundle(),
            new Gitonomy\Bundle\WebsiteBundle\GitonomyWebsiteBundle(),
            new Gitonomy\Bundle\GitBundle\GitonomyGitBundle(),
            new Gitonomy\Bundle\JobBundle\GitonomyJobBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Gitonomy\Bundle\DistributionBundle\GitonomyDistributionBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    protected function getKernelParameters()
    {
        return array_merge(
            parent::getKernelParameters(), array(
                'gitonomy.shell_command' => $this->getShellCommand(),
                'gitonomy.version'       => self::VERSION,
            )
        );
    }

    protected function getShellCommand()
    {
        return 'php '.realpath($this->getRootDir().'/console').' gitonomy:git';
    }
}
