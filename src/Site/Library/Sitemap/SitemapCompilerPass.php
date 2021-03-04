<?php

namespace WS\Site\Library\Sitemap;

use WS\Site\Service\SitemapService;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SitemapCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.site.sitemap_provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(SitemapService::class)) {
            return;
        }

        $definition = $container->findDefinition(SitemapService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerProvider', [$taggedService]);
        }
    }
}
