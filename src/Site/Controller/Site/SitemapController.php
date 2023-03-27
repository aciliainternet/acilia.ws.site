<?php

namespace WS\Site\Controller\Site;

use WS\Core\Service\ContextService;
use WS\Site\Service\SitemapService;
use WS\Core\Entity\Domain;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use WS\Core\Library\Storage\StorageDriverInterface;
use WS\Core\Service\StorageService;

/**
 * @Route(name="ws_site_seo_")
 */
class SitemapController extends AbstractController
{
    private SitemapService $sitemapService;
    private StorageService $storageService;
    private ParameterBagInterface $params;
    private ?Domain $domain = null;

    public function __construct(
        SitemapService $sitemapService,
        ContextService $contextService,
        StorageService $storageService,
        ParameterBagInterface $params
    ) {
        $this->sitemapService = $sitemapService;
        $this->storageService = $storageService;
        $this->params = $params;
        $this->domain = $contextService->getDomain();
    }

    /**
     * @Route("/{sitemap}.xml", methods="GET", name="sitemap", defaults={"extension": "xml"})
     * @Route("/{sitemap}.xml.gz", methods="GET", name="sitemap_compressed", defaults={"extension": "xml.gz"})
     */
    public function index(string $sitemap, string $extension): Response
    {
        $storagePrefix = '';
        if ($this->params->has('storage.configuration')) {
            $storagePrefix = $this->params->get('storage.configuration')['prefix'] ?? '';
        }

        $sitemapPath =  sprintf('%s/%s/%s.%s', $this->sitemapService->getPrefix(),  $this->domain->getHost(), $sitemap, $extension);
        $sitemapContent = $this->storageService->get($sitemapPath, StorageDriverInterface::CONTEXT_PRIVATE, ['prefix' => $storagePrefix]);

        // Generate response
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/xml');
        $response->headers->set('Content-Disposition', 'inline;');
        if ($extension === 'xml.gz') {
            $response->headers->set('Content-type', 'application/gzip');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $sitemapPath . '";');
        }

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent($sitemapContent);
        return $response;
    }
}
