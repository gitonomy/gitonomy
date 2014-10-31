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

namespace Gitonomy\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class AddValidationFilesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $dirs = array();
        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/config/validation')) {
                $dirs[] = $dir;
            }
        }
        if (is_dir($dir = $container->getParameter('kernel.root_dir').'/Resources/config/validation')) {
            $dirs[] = $dir;
        }

        $newFiles = array();
        if ($dirs) {
            $finder = new Finder();
            $finder->files()->name('*.xml')->in($dirs);
            foreach ($finder as $file) {
                $newFiles[] = $file->getPathName();
            }
        }

        $validatorBuilder = $container->getDefinition('validator.builder');

        if (count($newFiles) > 0) {
            $validatorBuilder->addMethodCall('addXmlMappings', array($newFiles));
        }
    }
}
