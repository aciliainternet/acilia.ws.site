<?php

namespace WS\Site\Widget;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use WS\Site\Entity\WidgetConfiguration;
use WS\Site\Library\Widget\WidgetInterface;

class FreeHTMLWidget implements WidgetInterface
{
    public function getId(): string
    {
        return 'free_html';
    }

    public function getIcon(): string
    {
        return 'fas fa-code';
    }

    public function getTranslationDomain(): string
    {
        return 'ws_cms_site';
    }

    public function getDefinition(FormBuilderInterface $builder): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'mapped' => false,
                'label' => 'form.freehtml.label',
                'required' => false,

            ]);
    }

    public function getTemplate(WidgetConfiguration $configuration): string
    {
        return '@WSSite/widgets/free_html.html.twig';
    }

    public function getData(WidgetConfiguration $configuration): array
    {
        $data = [
            'content' => '',
        ];

        $config = $configuration->getConfiguration();
        if (isset($config['content'])) {
            $data['content'] = $config['content'];
        }

        return $data;
    }

    public function getImageFields(): array
    {
        return [];
    }
}
