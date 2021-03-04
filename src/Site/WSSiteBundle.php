<?php

namespace WS\Site;

use WS\Site\Library\Metadata\MetadataCompilerPass;
use WS\Site\Library\Navbar\NavbarCompilerPass;
use WS\Site\Library\Sitemap\SitemapCompilerPass;
use WS\Site\Library\TranslationCompilerPass;
use WS\Site\Library\Widget\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WSSiteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslationCompilerPass());
        $container->addCompilerPass(new WidgetCompilerPass());
        $container->addCompilerPass(new NavbarCompilerPass());
        $container->addCompilerPass(new SitemapCompilerPass());
        $container->addCompilerPass(new MetadataCompilerPass());
    }
}
