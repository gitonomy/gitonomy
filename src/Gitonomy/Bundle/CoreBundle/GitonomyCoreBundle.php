<?php

namespace Gitonomy\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Gitonomy\Bundle\CoreBundle\DependencyInjection\Compiler\AddValidationFilesPass;

class GitonomyCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddValidationFilesPass());
    }
}
