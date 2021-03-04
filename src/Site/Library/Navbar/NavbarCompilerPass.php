<?php

namespace WS\Site\Library\Navbar;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WS\Site\Service\NavbarService;

class NavbarCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    const TAG = 'ws.site.navbar_definition';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(NavbarService::class)) {
            return;
        }

        $definition = $container->findDefinition(NavbarService::class);

        $taggedServices = $this->findAndSortTaggedServices(self::TAG, $container);
        foreach ($taggedServices as $taggedService) {
            $definition->addMethodCall('registerNavbarDefinition', [$taggedService]);
        }
    }
}
