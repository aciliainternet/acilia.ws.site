<?php

namespace WS\Site\Service;

use WS\Site\Library\Navbar\NavbarDefinition;
use WS\Site\Library\Navbar\NavbarDefinitionInterface;

class NavbarService
{
    protected array $services = [];
    protected ?array $navbar = null;

    public function registerNavbarDefinition(NavbarDefinitionInterface $service): void
    {
        $this->services[] = $service;
    }

    public function getNavbarDefinition(string $containerCode, ?string $contentCode = null): ?NavbarDefinition
    {
        // load navbar definitions when used
        $this->loadNavbarDefinitions();

        $navbarContainer = null;
        foreach ($this->navbar as $container) {
            if ($container->getCode() === $containerCode) {
                $navbarContainer = $container;
                break;
            }
        }

        if ($navbarContainer === null) {
            return null;
        }

        if ($contentCode === null) {
            return $navbarContainer;
        }

        /** @var NavbarDefinition $navbarContent */
        foreach ($navbarContainer->getChildren() as $navbarContent) {
            if ($navbarContent->getCode() === $contentCode) {
                return $navbarContent;
            }
        }

        return null;
    }

    public function removeNavbarDefinition(string $containerCode, ?string $contentCode = null): void
    {
        // load navbar definitions when used
        $this->loadNavbarDefinitions();

        foreach ($this->navbar as $keyContainer => $container) {
            if ($container->getCode() === $containerCode) {
                if ($contentCode !== null) {
                    /** @var NavbarDefinition $navbarContent */
                    foreach ($container->getChildren() as $navbarContent) {
                        if ($navbarContent->getCode() === $contentCode) {
                            $container->removeChild($navbarContent);
                            break;
                        }
                    }
                } else {
                    unset($this->navbar[$keyContainer]);
                }

                break;
            }
        }
    }

    public function getNavbar(): array
    {
        // load navbar definitions when used
        $this->loadNavbarDefinitions();

        $navbar = [];

        /** @var NavbarDefinition $navbarDefinition */
        foreach ($this->navbar as $navbarDefinition) {
            if (isset($navbar[$navbarDefinition->getCode()])) {
                foreach ($navbarDefinition->getChildren() as $menu) {
                    $navbar[$navbarDefinition->getCode()]->addChild($menu);
                }
            } else {
                $navbar[$navbarDefinition->getCode()] = $navbarDefinition;
            }
        }

        // order content menus
        foreach ($navbar as $menu) {
            if ($menu->isContainer()) {
                $navbarContents = $menu->getChildren();
                usort($navbarContents, fn(NavbarDefinition $menu1, NavbarDefinition $menu2) => strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder()));
                $menu->setChildren($navbarContents);
            }
        }

        // order containers menu
        usort($navbar, fn(NavbarDefinition $menu1, NavbarDefinition $menu2) => strcmp((string) $menu1->getOrder(), (string) $menu2->getOrder()));

        return $navbar;
    }

    protected function loadNavbarDefinitions(): void
    {
        if ($this->navbar === null) {
            $this->navbar = [];
            foreach ($this->services as $service) {
                foreach ($service->getNavbarDefinition() as $definition) {
                    if ($definition instanceof NavbarDefinition) {
                        $this->navbar[$definition->getCode()] = $definition;
                    }
                }
            }
        }
    }
}
