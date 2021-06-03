<?php

namespace WS\Site\Command\Sitemap;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\Core\Service\ContextService;
use WS\Core\Service\DomainService;
use WS\Site\Service\SitemapService;
use WS\Core\Service\SettingService;

class GenerateSitemapCommand extends Command
{
    private $sitemapService;
    private $settingService;
    private $domainService;
    private $router;
    private $contextService;
    private $projectDir;

    public function __construct(
        SitemapService $sitemapService,
        RouterInterface $router,
        SettingService $settingService,
        DomainService $domainService,
        ContextService $contextService,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();

        $this->sitemapService = $sitemapService;
        $this->settingService = $settingService;
        $this->domainService = $domainService;
        $this->router = $router;
        $this->contextService = $contextService;
        $this->projectDir = $parameterBag->get('kernel.project_dir');
    }

    protected function configure()
    {
        $this
            ->setName('ws:sitemap:generate')
            ->setDescription('Generate the xml sitemap of the site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $context = $this->router->getContext();

        if ($this->settingService->get('site_general_force_https')) {
            $context->setScheme('https');
        }

        $canonicalDomainsDone = [];
        foreach ($this->domainService->getCanonicals() as $canonicalDomain) {
            if (!isset($canonicalDomainsDone[$canonicalDomain->getHost()])) {
                $context->setHost($canonicalDomain->getHost());
                $this->contextService->setDomain($canonicalDomain);

                $domains = $this->domainService->getByHost($canonicalDomain->getHost());
                if (count($domains) > 1) {
                    $this->saveSitemaps($this->generateRootSitemapDomainLocale($domains), $canonicalDomain->getHost());
                    $canonicalDomainsDone[$domains[0]->getHost()] = true;

                    foreach ($domains as $domain) {
                        $this->saveSitemaps(
                            $this->sitemapService->getSitemap($domain->getLocale()),
                            $domain->getHost(),
                            $domain->getLocale()
                        );
                    }
                } else {
                    $this->saveSitemaps(
                        $this->sitemapService->getSitemap($canonicalDomain->getLocale()),
                        $canonicalDomain->getHost()
                    );
                }
            }

            $io->success(sprintf('"%s - %s" sitemap created/updated successfully', $canonicalDomain->getHost(), $canonicalDomain->getLocale()));
        }

        return 0;
    }

    private function generateRootSitemapDomainLocale($domains)
    {
        return $this->sitemapService->generateRootSitemapDomainLocale($domains);
    }

    private function saveSitemaps($xml, $host, $locale = null)
    {
        $sitemapPath = sprintf('%s/%s', $this->projectDir, $this->sitemapService->getRootPath());
        if (!file_exists($sitemapPath)) {
            mkdir($sitemapPath);
        }

        if (isset($xml['sitemap'])) {
            $path = sprintf('%s/%s/sitemap.xml', $sitemapPath, $host);
            if ($locale !== null) {
                $path = sprintf('%s/%s/sitemap-%s.xml', $sitemapPath, $host, $locale);
            }

            if (!is_dir(dirname($path))) {
                mkdir(dirname($path));
            }

            file_put_contents($path, $xml['sitemap']);
            file_put_contents($path.'.gz', zlib_encode($xml['sitemap'], ZLIB_ENCODING_GZIP));
        }

        if (isset($xml['sites'])) {
            foreach ($xml['sites'] as $key => $site) {
                $path = sprintf('%s/%s/sitemap-%s.xml', $sitemapPath, $host, $key + 1);
                if ($locale !== null) {
                    $path = sprintf('%s/%s/sitemap-%s-%s.xml', $sitemapPath, $host, $locale, $key + 1);
                }

                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path));
                }

                file_put_contents($path, $site);
                file_put_contents($path . '.gz', zlib_encode($site, ZLIB_ENCODING_GZIP));
            }
        }
    }
}
