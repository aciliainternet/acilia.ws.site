<?php

namespace WS\Site\Controller\Site;

use WS\Core\Service\ContextService;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(name="ws_site_seo_")
 */
class SitemapController extends AbstractController
{
    private $projectDir;
    private $domain;

    public function __construct(
        ContextService $contextService,
        ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = $parameterBag->get('kernel.project_dir');
        $this->domain = $contextService->getDomain();
    }

    /**
     * @Route("/{sitemap}.xml", methods="GET", name="sitemap", defaults={"extension": "xml"})
     * @Route("/{sitemap}.xml.gz", methods="GET", name="sitemap_compressed", defaults={"extension": "xml.gz"})
     */
    public function index(string $sitemap, string $extension)
    {
        try {
            $path =  sprintf('%s/public/site/sitemap/%s/', $this->projectDir, $this->domain->getHost());
            if ($path !== null) {
                return new BinaryFileResponse(sprintf('%s%s.%s', $path, $sitemap, $extension));
            }
        } catch (FileNotFoundException $e) {
        }

        throw new NotFoundHttpException();
    }
}
