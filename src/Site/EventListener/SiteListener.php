<?php

namespace WS\Site\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Service\ContextService;
use WS\Core\Service\SettingService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class SiteListener
{
    protected ContextService $contextService;
    protected SettingService $settingService;
    protected TranslatorInterface $translator;
    protected Environment $twig;

    public function __construct(
        ContextService $contextService,
        SettingService $settingService,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        $this->contextService = $contextService;
        $this->settingService = $settingService;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$event->getRequest()->isSecure() && $this->settingService->get('site_general_force_https')) {
            $secureUrl = sprintf('https://%s%s', $event->getRequest()->getHost(), $event->getRequest()->getRequestUri());
            $event->setResponse(new RedirectResponse($secureUrl, RedirectResponse::HTTP_MOVED_PERMANENTLY));
            return;
        }

        if ($this->contextService->isSite() && $this->settingService->get('site_general_in_maintenance')) {
            $event->setResponse(new Response($this->twig->render('@WSSite/site/maintenance.html.twig')));
            return;
        }
    }
}
