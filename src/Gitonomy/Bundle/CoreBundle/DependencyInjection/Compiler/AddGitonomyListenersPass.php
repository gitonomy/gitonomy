<?php

namespace Gitonomy\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddGitonomyListenersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('gitonomy_core.event_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('gitonomy_core.event_dispatcher');

        foreach ($container->findTaggedServiceIds('gitonomy.event_subscriber') as $id => $tags) {
            $definition->addMethodCall('addSubscriberService', array($id, $container->getDefinition($id)->getClass()));
        }

        foreach ($container->findTaggedServiceIds('gitonomy.event_listener') as $id => $tags) {
            foreach ($tags as $event) {
                $priority = isset($event['priority']) ? $event['priority'] : 0;

                if (!isset($event['event'])) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "event" attribute on "gitonomy.event_listener" tags.', $id));
                }

                if (!isset($event['method'])) {
                    $event['method'] = 'on'.preg_replace(array(
                        '/(?<=\b)[a-z]/ie',
                        '/[^a-z0-9]/i'
                    ), array('strtoupper("\\0")', ''), $event['event']);
                }

                $definition->addMethodCall('addListenerService', array($event['event'], array($id, $event['method']), $priority));
            }
        }
    }
}
