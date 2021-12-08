<?php

namespace WS\Site\EventListener;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;
use WS\Core\Service\ContextService;
use WS\Core\Service\SettingService;
use WS\Site\Service\RedirectionService;

class RedirectionListener
{
    protected RedirectionService  $redirectionService;
    protected ContextService $contextService;
    protected SettingService$settingService;

    public function __construct(
        RedirectionService $redirectionService,
        ContextService $contextService,
        SettingService $settingService
    ) {
        $this->redirectionService = $redirectionService;
        $this->contextService = $contextService;
        $this->settingService = $settingService;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->redirectionService->isEnabled()) {
            return;
        }

        if (!$this->contextService->isSite()) {
            return;
        }

        $url = $event->getRequest()->getPathInfo();
        if ($event->getRequest()->getQueryString() !== null) {
            $url = sprintf('%s?%s', $url, $event->getRequest()->getQueryString());
        }

        $cache = new FilesystemAdapter();
        $redirection = $cache->get(
            sprintf('ws_redirection_%s_%d', md5($url), $this->contextService->getDomain()->getId()),
            function (ItemInterface $item) use($url) {
                $item->expiresAfter(3600);

                return $this->redirectionService->getRedirection($url, $this->contextService->getDomain());
        });

        if (null !== $redirection) {
            $event->setResponse(new RedirectResponse(...$redirection));
        }
    }

    public function onController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->redirectionService->isEnabled()) {
            return;
        }

        $request = $event->getRequest();
        if (strpos($request->attributes->get('_route'), 'ws_cms_site_redirection') === 0) {
            throw new NotFoundHttpException();
        }
    }
}
