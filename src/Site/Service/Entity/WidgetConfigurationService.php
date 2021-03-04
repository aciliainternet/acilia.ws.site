<?php

namespace WS\Site\Service\Entity;

use WS\Site\Entity\WidgetConfiguration;
use WS\Site\Form\CMS\WidgetConfigurationType;
use WS\Core\Service\ContextService;
use WS\Core\Library\CRUD\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WS\Site\Service\WidgetService;

class WidgetConfigurationService extends AbstractService
{
    protected $widgetService;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, ContextService $contextService, WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
        parent::__construct($logger, $em, $contextService);
    }

    public function getEntityClass() : string
    {
        return WidgetConfiguration::class;
    }

    public function getFormClass(): ?string
    {
        return WidgetConfigurationType::class;
    }

    public function getSortFields() : array
    {
        return ['code'];
    }

    public function getImageEntityClass($entity) : ?string
    {
        if (!$entity instanceof WidgetConfiguration) {
            return null;
        }

        try {
            return get_class($this->widgetService->getWidget($entity->getWidget()));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImageFields($entity): array
    {
        assert(
            $entity instanceof WidgetConfiguration,
            sprintf('Entity must be of instance WidgetConfiguration but "%s" was provided.', get_class($entity))
        );

        try {
            $widget = $this->widgetService->getWidget($entity->getWidget());

            return $widget->getImageFields();
        } catch (\Exception $e) {
            return [];
        }
    }
}
