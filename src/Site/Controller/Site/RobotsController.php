<?php

namespace WS\Site\Controller\Site;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(name="ws_site_seo_")
 */
class RobotsController extends AbstractController
{
    /**
     * @Route("/robots.txt", name="robots")
     */
    public function robots(): Response
    {
        $response = $this->render('@WSSite/site/robots/robots.txt.twig');
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    /**
     * @Route("/humans.txt", name="humans")
     */
    public function humans(): Response
    {
        $response = $this->render('@WSSite/site/robots/humans.txt.twig');
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
