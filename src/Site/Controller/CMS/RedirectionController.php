<?php

namespace WS\Site\Controller\CMS;

use WS\Site\Service\Entity\RedirectionService;
use WS\Core\Library\CRUD\AbstractService;
use WS\Core\Library\CRUD\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/redirection", name="ws_cms_site_redirection_")
 */
class RedirectionController extends AbstractController
{
    public function __construct(RedirectionService $service)
    {
        $this->service = $service;
    }

    protected function getService(): AbstractService
    {
        return $this->service;
    }

    protected function useCRUDTemplate($template): bool
    {
        if ($template == 'index.html.twig') {
            return false;
        }

        return true;
    }
}
