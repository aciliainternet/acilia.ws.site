<?php

namespace WS\Site\DependencyInjection;

use WS\Site\Library\Metadata\MetadataCompilerPass;
use WS\Site\Library\Metadata\MetadataConsumerInterface;
use WS\Site\Library\Metadata\MetadataProviderInterface;
use WS\Site\Library\Navbar\NavbarCompilerPass;
use WS\Site\Library\Navbar\NavbarDefinitionInterface;
use WS\Site\Library\Sitemap\SitemapCompilerPass;
use WS\Site\Library\Sitemap\SitemapProviderInterface;
use WS\Site\Entity\Redirection;
use WS\Site\Entity\WidgetConfiguration;
use WS\Core\Library\CRUD\RoleCalculatorTrait;
use WS\Core\Library\CRUD\RoleLoaderTrait;
use WS\Core\Library\Traits\DependencyInjection\AddRolesTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use WS\Site\Library\Widget\WidgetCompilerPass;
use WS\Site\Library\Widget\WidgetInterface;
use WS\Site\Service\RedirectionService;
use WS\Site\Service\SitemapService;

class WSSiteExtension extends Extension
{
    use RoleCalculatorTrait;
    use RoleLoaderTrait;
    use AddRolesTrait;

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('widgets.yaml');

        $masterRole = 'ROLE_WS_SITE';
        $actions = ['view', 'create', 'edit', 'delete'];
        $entities = [
            WidgetConfiguration::class,
            Redirection::class
        ];

        $this->loadRoles($container, $masterRole, $entities, $actions);

        $this->addRoles($container, [
            'ROLE_WS_SITE' => ['ROLE_WS_SITE_SETTING', 'ROLE_WS_SITE_WIDGETS', 'ROLE_WS_SITE_WIDGETCONFIGURATION']
        ]);

        // Tag Site Widgets
        $container->registerForAutoconfiguration(WidgetInterface::class)->addTag(WidgetCompilerPass::TAG);

        // Tag Sitemap Providers
        $container->registerForAutoconfiguration(SitemapProviderInterface::class)->addTag(SitemapCompilerPass::TAG);

        // Tag Metadata Providers
        $container->registerForAutoconfiguration(MetadataProviderInterface::class)->addTag(MetadataCompilerPass::TAG_PROVIDER);

        // Tag Metadata Consumers
        $container->registerForAutoconfiguration(MetadataConsumerInterface::class)->addTag(MetadataCompilerPass::TAG_CONSUMER);

        // Tag Navigation Bar Definition
        $container->registerForAutoconfiguration(NavbarDefinitionInterface::class)->addTag(NavbarCompilerPass::TAG);

        // Configure services
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Configure Redirection
        $redirectionService = $container->getDefinition(RedirectionService::class);
        $redirectionService->setArgument(0, $config['redirection']);

        // Configure Sitemap
        $sitemapeService = $container->getDefinition(SitemapService::class);
        $sitemapeService->setArgument(0, $config['sitemap']);
    }
}
