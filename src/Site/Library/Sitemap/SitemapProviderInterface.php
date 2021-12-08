<?php

namespace WS\Site\Library\Sitemap;

interface SitemapProviderInterface
{
    public function getRoutes(string $locale): array;
}
