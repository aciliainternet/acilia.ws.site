<?php

namespace WS\Site\Twig\Extension;

use WS\Site\Service\WidgetService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetExtension extends AbstractExtension
{
    protected WidgetService $widgetService;

    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_widget', [$this, 'renderWidget'], ['is_safe' => ['html']]),
        ];
    }

    public function renderWidget(string $code, array $context = []): ?string
    {
        return $this->widgetService->render($code, $context);
    }
}
