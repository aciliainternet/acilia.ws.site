<?php

namespace WS\Site\Library\Metadata;

use WS\Site\Service\MetadataConsumerService;
use WS\Site\Service\MetadataService;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MetadataCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG_PROVIDER = 'ws.site.metadata_provider';
    const TAG_CONSUMER = 'ws.site.metadata_consumer';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(MetadataService::class)) {
            return;
        }

        $metadataService = $container->findDefinition(MetadataService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG_PROVIDER, $container);
        foreach ($taggedServices as $taggedService) {
            $metadataService->addMethodCall('registerProvider', [$taggedService]);
        }

        // Metadata Consumers
        if ($container->has(MetadataConsumerService::class)) {
            $metadataConsumerService = $container->findDefinition(MetadataConsumerService::class);

            $consumers = $this->findAndSortTaggedServices(self::TAG_CONSUMER, $container);
            foreach ($consumers as $consumerReference) {
                $consumer = $container->findDefinition($consumerReference);
                $consumer->addMethodCall('setMetadataConsumerService', [$metadataConsumerService]);
            }
        }
    }
}
