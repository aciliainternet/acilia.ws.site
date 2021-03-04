<?php

namespace WS\Site\Library;

use WS\Core\Service\TranslationService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TranslationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(TranslationService::class)) {
            return;
        }

        $sourcePath = realpath(sprintf('%s/../Resources/translations', __DIR__));

        $definition = $container->findDefinition(TranslationService::class);
        $definition->addMethodCall('addSource', [$sourcePath, 'ws']);
    }
}
