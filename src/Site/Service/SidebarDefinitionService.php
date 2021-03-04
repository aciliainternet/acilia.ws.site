<?php

namespace WS\Site\Service;

use WS\Core\Library\Sidebar\SidebarDefinition;
use WS\Core\Library\Sidebar\SidebarDefinitionInterface;

class SidebarDefinitionService implements SidebarDefinitionInterface
{
    protected $redirectionService;

    public function __construct(RedirectionService $redirectionService)
    {
        $this->redirectionService = $redirectionService;
    }

    public function getSidebarDefinition(): array
    {
        $siteNode = new SidebarDefinition(
            'site',
            'menu',
            null,
            [
                'container' => SidebarDefinition::SIDEBAR_CONTAINER,
                'roles' => [
                    'ROLE_WS_SITE_PAGE_VIEW',
                    'ROLE_WS_SITE_WIDGETS'
                ],
                'translation_domain' => 'ws_cms_site',
                'icon' => 'fa-globe',
                'collapsed_routes' => ['ws_cms_site_'],
                'order' => 3
            ]
        );

        // widgets menu
        $siteNode->addChild(new SidebarDefinition(
            'widgets',
            'menu',
            [
                'route_name' => 'ws_cms_site_widget_index'
            ],
            [
                'roles' => ['ROLE_WS_SITE_WIDGETS'],
                'translation_domain' => 'ws_cms_site_widget',
                'icon' => 'fa-drafting-compass',
                'collapsed_routes' => ['ws_cms_site_widget_'],
                'order' => 2
            ]
        ));

        if ($this->redirectionService->isEnabled()) {
            // redirection menu
            $redirectionsNode = new SidebarDefinition(
                'redirections',
                'menu',
                [
                    'route_name' => 'ws_cms_site_redirection_index'
                ],
                [
                    'roles' => ['ROLE_WS_SITE_REDIRECTION'],
                    'translation_domain' => 'ws_cms_site_redirection',
                    'icon' => 'fa-random',
                    'collapsed_routes' => ['ws_cms_site_redirection_'],
                    'order' => 4
                ]
            );

            return [
                $siteNode, $redirectionsNode
            ];
        }

        return [
            $siteNode
        ];
    }
}
