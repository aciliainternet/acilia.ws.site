<?php

namespace WS\Site\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WS\Core\Service\ContextService;
use WS\Core\Service\SettingService;
use WS\Site\Service\RedirectionService;

class RedirectionListener
{
    protected $redirectionService;
    protected $contextService;
    protected $settingService;

    public function __construct(RedirectionService $redirectionService, ContextService $contextService, SettingService $settingService)
    {
        $this->redirectionService = $redirectionService;
        $this->contextService = $contextService;
        $this->settingService = $settingService;
    }

    public function onRequest(RequestEvent $event)
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

        $redirection = $this->redirectionService->getRedirection($url, $this->contextService->getDomain()->getId());
        if ($redirection instanceof RedirectResponse) {
            $event->setResponse($redirection);
        }
    }

    public function onController(ControllerEvent $event)
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
