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
     *
     * @return Response
     * @throws \Exception
     */
    public function robots()
    {
        $response = $this->render('@WSSite/site/robots/robots.txt.twig');
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    /**
     * @Route("/humans.txt", name="humans")
     *
     * @return Response
     * @throws \Exception
     */
    public function humans()
    {
        $response = $this->render('@WSSite/site/robots/humans.txt.twig');
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
}
