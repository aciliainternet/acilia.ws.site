<?php

namespace WS\Site\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WS\Site\Library\Sitemap\Url;
use WS\Site\Library\Sitemap\SitemapProviderInterface;

class SitemapService
{
    protected array $config;
    protected ?array $providers = null;
    protected UrlGeneratorInterface $router;

    public function __construct(array $config, UrlGeneratorInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    public function registerProvider(SitemapProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    public function getRootPath(string $path = 'sitemap'): string
    {
        return sprintf('%s/%s/', $this->config['root'], $path);
    }

    public function getSitemap($locale): array
    {
        $xml = [];
        $root = $this->generateUrlSitemapDocument();
        $urls = $this->getUrls($locale);

        if (count($urls) < 100) {
            $this->createUrlSet($root, $urls);
            $xml['sitemap'] = $root->saveXML();
        } else {
            $urlsChunk = array_chunk($urls, 100);
            $sitemap = $this->createSitemapIndex(count($urlsChunk), $locale);
            $xml['sitemap'] = $sitemap->saveXML();

            foreach ($urlsChunk as $urls) {
                $sitemap = $this->generateUrlSitemapDocument();
                $this->createUrlSet($sitemap, $urls);
                $xml['sites'][] = $sitemap->saveXML();
            }
        }

        return $xml;
    }

    public function generateRootSitemapDomainLocale(array $domains): array
    {
        $root = $this->generateUrlSitemapDocument();
        $urlSet = $root->createElement('sitemapindex');
        foreach ($domains as $domain) {
            $child = $this->getChildSitemapXml($root, $domain->getLocale());
            $urlSet->appendChild($child);
        }
        $root->appendChild($urlSet);
        $xml['sitemap'] = $root->saveXML();

        return $xml;
    }

    protected function createSitemapIndex($length, $locale = null): \DOMDocument
    {
        $root = $this->generateUrlSitemapDocument();

        $urlSet = $root->createElement('sitemapindex');
        for ($i = 0; $i < $length; $i++) {
            $strIndex = (string) ($i + 1);
            $child = $this->getChildSitemapXml($root, $strIndex);
            if ($locale !== null) {
                $child = $this->getChildSitemapXml($root, sprintf('%s-%s', $locale, $strIndex));
            }

            $urlSet->appendChild($child);
        }
        $root->appendChild($urlSet);

        return $root;
    }

    protected function createUrlSet(\DOMDocument $root, array $urls): void
    {
        $urlSet = $root->createElement('urlset');

        foreach ($urls as $url) {
            $urlSet->appendChild($url->getXml($root, $urlSet));
        }

        $root->appendChild($urlSet);
    }

    protected function generateUrlSitemapDocument(): \DOMDocument
    {
        $sitemap = new \DOMDocument('1.0', 'UTF-8');
        $sitemap->preserveWhiteSpace = false;
        $sitemap->formatOutput = true;

        return $sitemap;
    }

    protected function getChildSitemapXml(\DOMDocument $root, string $index): \DOMElement
    {
        try {
            $now = new \DateTime('now');
            $nodeDate = $now->format('Y-m-d');
        } catch (\Exception $e) {
            $nodeDate = '';
        }

        $url = $root->createElement('sitemap');
        $url->appendChild(
            new \DOMElement(
                'loc',
                $this->router->generate(
                    'ws_site_seo_sitemap',
                    ['sitemap' => sprintf('sitemap-%s', $index)],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
        );

        $url->appendChild(new \DOMElement('lastmod', $nodeDate));

        return $url;
    }

    protected function getUrls($locale): array
    {
        $routes = $this->getRoutes($locale);
        $urls = [];
        foreach ($routes as $route) {
            if (!isset($route['modified'])) {
                try {
                    $route['modified'] = new \DateTime('now');
                } catch (\Exception $e) {
                    $route['modified'] = null;
                }
            }

            $routeName = $route['route']['name'] ??  '';
            $routeParameters = $route['route']['parameters'] ?? [];
            $referenceType = $route['route']['reference'] ?? UrlGeneratorInterface::ABSOLUTE_URL;
            $url = $this->router->generate($routeName, $routeParameters, $referenceType);

            if (isset($route['url'])) {
                $url = $route['url'];
            }
            $urls[] = $this->getUrlElement($url, $route['frequency'], $route['priority'], $route['modified']);
        }

        return $urls;
    }

    protected function getRoutes($locale): array
    {
        $routes = [];

        if (null === $this->providers) {
            return $routes;
        }

        foreach ($this->providers as $provider) {
            $providerRoutes = $provider->getRoutes($locale);
            foreach ($providerRoutes as $route) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

    protected function getUrlElement(string $route, string $frequency, float $priority, \DateTime $lastModified = null): Url
    {
        $url = new Url();
        $url->setUrl($route)
            ->setFrequency($frequency)
            ->setPriority($priority);

        if (!$lastModified instanceof \DateTime) {
            try {
                $url->setLastModified(new \DateTime('now'));
            } catch (\Exception $e) {
            }
        }

        return $url;
    }
}
