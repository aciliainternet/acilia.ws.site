<?php

namespace WS\Site\Form\CMS;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use WS\Core\Entity\AssetImage;
use WS\Core\Library\Asset\Form\AssetImageType;
use WS\Core\Library\Publishing\PublishingFormTrait;
use WS\Core\Service\FactoryCollectorService;
use WS\Site\Library\Widget\WidgetInterface;
use WS\Site\Entity\WidgetConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetConfigurationType extends AbstractType
{
    use PublishingFormTrait;

    protected $factoryService;

    public function __construct(FactoryCollectorService $factoryService)
    {
        $this->factoryService = $factoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['widget'] === null) {
            throw new InvalidConfigurationException('The options "widget" is required.');
        }

        if (!$options['widget'] instanceof WidgetInterface) {
            throw new InvalidConfigurationException('The options "widget" must implement WidgetInterface.');
        }

        $builder
            ->add('code', TextType::class, [
                'label' => 'form.code.label',
                'attr' => [
                    'placeholder' => 'form.code.placeholder',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'form.name.label',
                'attr' => [
                    'placeholder' => 'form.name.placeholder',
                ],
            ])
        ;

        $this->addPublishingFields($builder);

        // load custom widget form
        $options['widget']->getDefinition($builder);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var WidgetConfiguration $widgetConfiguration */
            $widgetConfiguration = $event->getData();
            $config = $widgetConfiguration->getConfiguration();

            foreach ($event->getForm()->all() as $key => $child) {
                if ($child->getConfig()->getType()->getInnerType() instanceof AssetImageType) {
                    if (!isset($config[$key])) {
                        $config[$key] = null;
                    }
                } elseif ($child->getConfig()->getType()->getInnerType() instanceof EntityType) {
                    $config[$key] = null;
                    if ($child->getData() !== null) {
                        $config[$key] = $child->getData()->getId();
                    }
                } else {
                    $config[$key] = $child->getData();
                }
            }

            $widgetConfiguration->setConfiguration($config);
        });


        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $widgetConfiguration = $event->getData();
            $config = $widgetConfiguration->getConfiguration();

            // get a factory collector
            $factoryCollector = $this->factoryService->getCollector();

            foreach ($event->getForm()->all() as $key => $child) {
                if ($child->getConfig()->getOptions()['mapped']) {
                    continue;
                }

                if (! isset($config[$key])) {
                    continue;
                }

                if ($child->getConfig()->getType()->getInnerType() instanceof EntityType) {
                    if ($config[$key] !== null) {
                        // add the entity to the collector
                        $factoryCollector->add($child->getConfig()->getOption('class'), [$config[$key]]);
                    }
                }

                if ($child->getConfig()->getType()->getInnerType() instanceof AssetImageType) {
                    if ($config[$key] !== null) {
                        // add the entity to the collector
                        $factoryCollector->add(AssetImage::class, [$config[$key]]);
                    }
                }
            }

            // fetch objects
            $configData = $factoryCollector->fetch();

            foreach ($event->getForm()->all() as $key => $child) {
                if ($child->getConfig()->getOptions()['mapped']) {
                    continue;
                }

                if (! isset($config[$key])) {
                    continue;
                }

                if ($child->getConfig()->getType()->getInnerType() instanceof EntityType) {
                    if ($config[$key] !== null) {
                        if (isset($configData[$child->getConfig()->getOption('class')])) {
                            // populate the form with objects
                            $event->getForm()->get($key)->setData($configData[$child->getConfig()->getOption('class')][$config[$key]]);
                        }
                    }
                } elseif ($child->getConfig()->getType()->getInnerType() instanceof AssetImageType) {
                    if ($config[$key] !== null) {
                        if (isset($configData[AssetImage::class])) {
                            // populate asset image object
                            $event->getForm()->get($key)->get('asset_image')->setData($configData[AssetImage::class][$config[$key]]);
                        }
                    }
                } else {
                    $event->getForm()->get($key)->setData($config[$key]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WidgetConfiguration::class,
            'widget' => null,
            'attr' => [
                'novalidate' => 'novalidate',
                'autocomplete' => 'off',
                'accept-charset'=> 'UTF-8'
            ]
        ]);
    }
}
