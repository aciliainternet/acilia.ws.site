<?php

namespace WS\Site\Library\Sitemap;

interface SitemapProviderInterface
{
    public function getRoutes($locale) : array;
}
