<?php

namespace WS\Site\Service;

use WS\Site\Entity\Redirection;
use WS\Site\Service\Entity\RedirectionService as RedirectionEntityService;
use Symfony\Component\HttpFoundation\Response;
use WS\Core\Entity\Domain;

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

    public function getRedirection(string $url, ?Domain $domain): ?array
    {
        // search for exact redirection
        $redirection = $this->redirectionEntityService->getExactRedirection($url, $domain);
        if (null === $redirection) {
            // search for regex redirection
            $redirection = $this->redirectionEntityService->getRegexRedirection($url, $domain);
        }

        if ($redirection instanceof Redirection) {
            return [
                $redirection->getDestination(),
                Response::HTTP_MOVED_PERMANENTLY,
                [self::REDIRECTION_ID => $redirection->getId()]
            ];
        }

        return null;
    }
}
