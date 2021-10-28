<?php

namespace WS\Site\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use WS\Site\Service\NavbarService;

class NavbarExtension extends AbstractExtension
{
    private NavbarService $navbarService;

    public function __construct(NavbarService $navbarService)
    {
        $this->navbarService = $navbarService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_navbar', [$this, 'getNavbar'])
        ];
    }

    public function getNavbar(): array
    {
        return $this->navbarService->getNavbar();
    }
}
