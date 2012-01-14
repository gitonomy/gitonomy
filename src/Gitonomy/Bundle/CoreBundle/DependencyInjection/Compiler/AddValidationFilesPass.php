<?php

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
        if($dirs) {
            $finder = new Finder();
            $finder->files()->name('*.xml')->in($dirs);
            foreach ($finder as $file) {
                $newFiles[] = $file->getPathName();
            }
        }

        $files = array_merge($container->getParameter('validator.mapping.loader.xml_files_loader.mapping_files'), $newFiles);


        $container->setParameter('validator.mapping.loader.xml_files_loader.mapping_files', $files);
    }
}
