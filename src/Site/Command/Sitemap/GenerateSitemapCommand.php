<?php

namespace WS\Site\Command\Sitemap;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\Core\Library\Storage\StorageDriverInterface;
use WS\Core\Service\ContextService;
use WS\Core\Service\DomainService;
use WS\Site\Service\SitemapService;
use WS\Core\Service\SettingService;
use WS\Core\Service\StorageService;

class GenerateSitemapCommand extends Command
{
    private SitemapService $sitemapService;
    private RouterInterface $router;
    private SettingService $settingService;
    private DomainService $domainService;
    private ContextService $contextService;
    private StorageService $storageService;

    public function __construct(
        SitemapService $sitemapService,
        RouterInterface $router,
        SettingService $settingService,
        DomainService $domainService,
        ContextService $contextService,
        StorageService $storageService
    ) {
        parent::__construct();

        $this->sitemapService = $sitemapService;
        $this->settingService = $settingService;
        $this->domainService = $domainService;
        $this->router = $router;
        $this->contextService = $contextService;
        $this->storageService = $storageService;
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
                        $this->contextService->setDomain($domain);

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

            $io->success(sprintf(
                '"%s - %s" sitemap created/updated successfully',
                $canonicalDomain->getHost(),
                $canonicalDomain->getLocale())
            );
        }

        return 0;
    }

    private function generateRootSitemapDomainLocale(array $domains): ?array
    {
        return $this->sitemapService->generateRootSitemapDomainLocale($domains);
    }

    private function saveSitemaps(array $xml, ?string $host, ?string $locale = null): void
    {
        $sitemapPrefix = $this->sitemapService->getPrefix();
        if (isset($xml['sitemap'])) {
            $path = sprintf('%s/%s/sitemap.xml', $sitemapPrefix, $host);
            if ($locale !== null) {
                $path = sprintf('%s/%s/sitemap-%s.xml', $sitemapPrefix, $host, $locale);
            }

            $this->storageService->save($path, $xml['sitemap'], StorageDriverInterface::CONTEXT_PRIVATE);
            $this->storageService->save($path.'.gz', zlib_encode($xml['sitemap'], ZLIB_ENCODING_GZIP), StorageDriverInterface::CONTEXT_PRIVATE);
        }

        if (isset($xml['sites'])) {
            foreach ($xml['sites'] as $key => $site) {
                $path = sprintf('%s/%s/sitemap-%s.xml', $sitemapPrefix, $host, (string)($key + 1));
                if ($locale !== null) {
                    $path = sprintf('%s/%s/sitemap-%s-%s.xml', $sitemapPrefix, $host, $locale, (string)($key + 1));
                }

                $this->storageService->save($path, $site, StorageDriverInterface::CONTEXT_PRIVATE);
                $this->storageService->save($path . '.gz', zlib_encode($site, ZLIB_ENCODING_GZIP), StorageDriverInterface::CONTEXT_PRIVATE);
            }
        }
    }
}
