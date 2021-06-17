<?php

namespace WS\Site\Library\Navbar;

class NavbarDefinition
{
    private $code;
    private $label;
    private $description;
    private $route;
    protected $children;
    protected $options;

    public function __construct(
        string $code,
        string $label,
        string $description = null,
        array $route = null,
        array $options = []
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->description = $description;
        $this->route = $route;
        $this->children = [];

        $this->options = array_merge([
            'order' => 999,
            'collapsed_routes' => [],
            'class' => '',
            'hidden_mobile' => false,
            'translate' => true
        ], $options);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return ($this->description === null) ? '' : $this->description;
    }

    public function isContainer(): bool
    {
        return !empty($this->getChildren());
    }

    public function hasRoute(): bool
    {
        if (isset($this->route['route_name'])) {
            return true;
        }

        return false;
    }

    public function getRouteName(): ?string
    {
        if (isset($this->route['route_name'])) {
            return $this->route['route_name'];
        }

        return '#';
    }

    public function getRouteOptions(): array
    {
        if (isset($this->route['route_options'])) {
            return $this->route['route_options'];
        }

        return [];
    }

    public function getCollapsedRoutes(): array
    {
        return $this->options['collapsed_routes'];
    }

    public function setCollapsedRoutes(array $collapsedRoutes): NavbarDefinition
    {
        $this->options['collapsed_routes'] = $collapsedRoutes;

        return $this;
    }

    public function addCollapsedRoute(string $collapsedRoute): NavbarDefinition
    {
        $this->options['collapsed_routes'][] = $collapsedRoute;

        return $this;
    }

    public function addChild(NavbarDefinition $child): void
    {
        $this->children[] = $child;
    }

    public function removeChild(NavbarDefinition $child): void
    {
        foreach ($this->children as $childKey => $childValue) {
            if ($child->getCode() === $childValue->getCode()) {
                unset($this->children[$childKey]);
                break;
            }
        }
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): NavbarDefinition
    {
        $this->children = $children;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->options['order'];
    }

    public function getClass(): string
    {
        return $this->options['class'];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
