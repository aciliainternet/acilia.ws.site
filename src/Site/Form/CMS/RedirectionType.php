<?php

namespace WS\Site\Form\CMS;

use WS\Core\Entity\Domain;
use WS\Site\Entity\Redirection;
use WS\Core\Library\Form\ToggleChoiceType;
use WS\Site\Library\Metadata\MetadataFormTrait;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RedirectionType extends AbstractType
{
    use MetadataFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', EntityType::class, [
                'label' => 'fields.domain.label',
                'multiple' => false,
                'class' => Domain::class,
                'required' => false,
                'placeholder' => 'fields.domain.placeholder',
                'attr' => [
                    'data-component' => 'ws_select',
                    'data-search' => false
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Domain::CANONICAL)
                        ->orderBy('d.host', 'ASC');
                },
            ])
            ->add('origin', TextType::class, [
                'label' => 'fields.origin.label',
                'attr' => [
                    'placeholder' => 'fields.origin.placeholder',
                ],
            ])
            ->add('destination', TextType::class, [
                'label' => 'fields.destination.label',
                'attr' => [
                    'placeholder' => 'fields.destination.placeholder',
                ],
            ])
            ->add('exactMatch', ToggleChoiceType::class, [
                'label' => 'fields.exactMatch.label',
                'choices' => [
                    'form.exactMatch.option.no.label' => false,
                    'form.exactMatch.option.yes.label' => true,
                ],
                'row_attr' => [
                    'class' => 'l-form__item--small',
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $origin = $data['origin'];
            if (strpos($origin, 'https://') !== 0 && strpos($origin, 'http://') !== 0 && strpos($origin, '/') !== 0) {
                $data['origin'] = '/' . $origin;
            }
            $destination = $data['destination'];

            if (strpos($destination, 'https://') !== 0 && strpos($destination, 'http://') !== 0 && strpos($destination, '/') !== 0) {
                $data['destination'] = '/'. $destination;
            }
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Redirection::class
        ]);
    }
}
