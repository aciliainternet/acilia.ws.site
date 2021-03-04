<?php

namespace WS\Site\Library\Widget;

use WS\Site\Service\WidgetService;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class WidgetCompilerPass implements CompilerPassInterface
{
    const TAG = 'ws.site.widget';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(WidgetService::class)) {
            return;
        }

        $definition = $container->findDefinition(WidgetService::class);

        foreach ($container->findTaggedServiceIds(self::TAG, true) as $serviceId => $attributes) {
            if (isset($attributes[0]) && isset($attributes[0]['disabled']) && $attributes[0]['disabled']) {
                continue;
            } else {
                $taggedService = new Reference($serviceId);
                $definition->addMethodCall('addWidget', [$taggedService]);
            }
        }
    }
}
