<?php

namespace WS\Site\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WS\Site\Library\Sitemap\Url;
use WS\Site\Library\Sitemap\SitemapProviderInterface;

class SitemapService
{
    /**
     * @var SitemapProviderInterface[];
     */
    protected $providers = null;
    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function registerProvider(SitemapProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    public function getSitemap($locale)
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

    public function generateRootSitemapDomainLocale(array $domains)
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

    protected function createSitemapIndex($length, $locale = null)
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

    protected function createUrlSet(\DOMDocument $root, array $urls)
    {
        $urlSet = $root->createElement('urlset');

        foreach ($urls as $url) {
            $urlSet->appendChild($url->getXml($root, $urlSet));
        }

        $root->appendChild($urlSet);
    }

    protected function generateUrlSitemapDocument() : \DOMDocument
    {
        $sitemap = new \DOMDocument('1.0', 'UTF-8');
        $sitemap->preserveWhiteSpace = false;
        $sitemap->formatOutput = true;

        return $sitemap;
    }

    protected function getChildSitemapXml(\DOMDocument $root, string $index)
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
                    'sitemap',
                    ['sitemap' => sprintf('sitemap-%s', $index)],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
        );

        $url->appendChild(new \DOMElement('lastmod', $nodeDate));

        return $url;
    }

    protected function getUrls($locale)
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

    protected function getRoutes($locale)
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

    protected function getUrlElement($route, $frequency, $priority, \DateTime $lastModified = null)
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
