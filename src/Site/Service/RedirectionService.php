<?php

namespace WS\Site\Service;

use WS\Site\Entity\Redirection;
use WS\Site\Service\Entity\RedirectionService as RedirectionEntityService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectionService
{
    protected $redirectionEntityService;
    protected $enabled;

    const REDIRECTION_ID = 'x-ws-redirection-id';

    public function __construct($enabled, RedirectionEntityService $redirectionService)
    {
        $this->redirectionEntityService = $redirectionService;
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getRedirection(string $url, string $host): ?RedirectResponse
    {
        $redirection = $this->redirectionEntityService->getValidRedirection($url, $host);

        if ($redirection instanceof Redirection) {
            return new RedirectResponse(
                $redirection->getDestination(),
                Response::HTTP_MOVED_PERMANENTLY,
                [
                    self::REDIRECTION_ID => $redirection->getId()
                ]
            );
        }

        return null;
    }
}
