<?php

namespace WS\Site\Widget;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use WS\Core\Service\ContextService;
use WS\Site\Entity\WidgetConfiguration;
use WS\Site\Library\Widget\WidgetInterface;
use WS\Site\Repository\WidgetConfigurationRepository;

class WidgetCollectionWidget implements WidgetInterface
{
    protected ContextService $contextService;

    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public function getId(): string
    {
        return 'widget_collection';
    }

    public function getIcon(): string
    {
        return 'fas fa-bars';
    }

    public function getTranslationDomain(): string
    {
        return 'ws_cms_site';
    }

    public function getDefinition(FormBuilderInterface $builder): void
    {
        $builder
            ->add('widgets', EntityType::class, [
                'mapped' => false,
                'class' => WidgetConfiguration::class,
                'multiple' => true,
                'label' => 'form.collection.label',
                'required' => false,
                'attr' => [
                    'data-component' => 'ws_select',
                    'data-search' => true
                ],
                'query_builder' => function (WidgetConfigurationRepository $er) {
                    return $er->getAvailableQuery($this->contextService->getDomain());
                }
            ]);
    }

    public function getTemplate(WidgetConfiguration $configuration): string
    {
        return '@WSSite/widgets/widget_collection.html.twig';
    }

    public function getData(WidgetConfiguration $configuration): array
    {
        $data = [
            'widgets' => [],
            'current_code' => $configuration->getCode()
        ];

        $config = $configuration->getConfiguration();
        if (isset($config['widgets']) && is_array($config['widgets'])) {
            $data['widgets'] = $config['widgets'];
        }

        return $data;
    }

    public function getImageFields(): array
    {
        return [];
    }
}
