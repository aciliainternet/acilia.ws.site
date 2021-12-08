<?php

namespace WS\Site\Service;

use WS\Core\Library\ActivityLog\ActivityLogInterface;
use WS\Core\Library\Publishing\PublishingEntityInterface;
use WS\Core\Service\ContextService;
use WS\Site\Entity\WidgetConfiguration;
use WS\Site\Library\Widget\WidgetInterface;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class WidgetService implements ActivityLogInterface
{
    protected bool $debug = false;
    protected EntityManagerInterface $em;
    protected Environment $twig;
    protected ContextService $contextService;
    protected array $widgets = [];
    protected ?array $widgetConfigurations = null;

    public function __construct(
        bool $debug,
        EntityManagerInterface $em,
        Environment $twig,
        ContextService $contextService
    ) {
        $this->debug = $debug;
        $this->em = $em;
        $this->twig = $twig;
        $this->contextService = $contextService;
    }

    public function addWidget(WidgetInterface $widget): void
    {
        $this->widgets[$widget->getId()] = $widget;
    }

    public function isRegistered(string $id): bool
    {
        return array_key_exists($id, $this->widgets);
    }

    public function getWidget(string $id): WidgetInterface
    {
        if (!array_key_exists($id, $this->widgets)) {
            throw new \Exception(sprintf('There is no Widget registered with id "%s"', $id));
        }

        return $this->widgets[$id];
    }

    public function getWidgets(): array
    {
        $widgets = $this->widgets;

        usort($widgets, function (WidgetInterface $a, WidgetInterface $b) {
            return strcmp($a->getId(), $b->getId());
        });

        return $widgets;
    }

    public function render(string $code, array $context): ?string
    {
        $configuration = $this->getWidgetConfiguration($code);

        if (! $configuration instanceof WidgetConfiguration) {
            return sprintf(' <!-- Widget Configuration with code "%s" not found --> ', $code);
        }

        if (! $this->isRegistered($configuration->getWidget())) {
            return sprintf(' <!-- Widget with id "%s" not registered --> ', $configuration->getWidget());
        }

        try {
            $widget = $this->getWidget($configuration->getWidget());
            $template = $widget->getTemplate($configuration);
            $data = $widget->getData($configuration);

            $data['ws_context'] = $context;

            return $this->twig->render($template, $data);
        } catch (\Exception $e) {
            if (true === $this->debug) {
                throw $e;
            }
        }

        return sprintf(' <!-- Widget with id "%s" cannot be loaded --> ', $configuration->getWidget());
    }

    public function getActivityLogSupported(): string
    {
        return WidgetConfiguration::class;
    }

    public function getActivityLogFields(): ?array
    {
        return ['name', 'widget', 'configuration'];
    }

    private function getWidgetConfiguration(string $code): ?WidgetConfiguration
    {
        if (null === $this->widgetConfigurations) {
            $widgetConfigurations = $this->em->getRepository(WidgetConfiguration::class)->findBy([
                'publishStatus' => PublishingEntityInterface::STATUS_PUBLISHED,
                'domain' => $this->contextService->getDomain()
            ]);

            foreach ($widgetConfigurations as $widget) {
                $this->widgetConfigurations[$widget->getCode()] = $widget;
            }
        }

        return $this->widgetConfigurations[$code] ?? null;
    }
}
