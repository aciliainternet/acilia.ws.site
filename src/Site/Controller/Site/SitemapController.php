<?php

namespace WS\Site\Controller\Site;

use WS\Core\Service\ContextService;
use WS\Site\Service\SitemapService;
use WS\Core\Entity\Domain;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(name="ws_site_seo_")
 */
class SitemapController extends AbstractController
{
    private SitemapService $sitemapService;
    private string $projectDir;
    private ?Domain $domain = null;

    public function __construct(
        ContextService $contextService,
        SitemapService $sitemapService,
        ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = $parameterBag->get('kernel.project_dir');
        $this->domain = $contextService->getDomain();
        $this->sitemapService = $sitemapService;
    }

    /**
     * @Route("/{sitemap}.xml", methods="GET", name="sitemap", defaults={"extension": "xml"})
     * @Route("/{sitemap}.xml.gz", methods="GET", name="sitemap_compressed", defaults={"extension": "xml.gz"})
     */
    public function index(string $sitemap, string $extension): Response
    {
        try {
            $path =  sprintf('%s/%s/%s/', $this->projectDir, $this->sitemapService->getRootPath(), $this->domain->getHost());
            if ($path !== null) {
                return new BinaryFileResponse(sprintf('%s%s.%s', $path, $sitemap, $extension));
            }
        } catch (FileNotFoundException $e) {
        }

        throw new NotFoundHttpException();
    }
}
