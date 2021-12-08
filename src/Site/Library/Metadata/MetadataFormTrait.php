<?php

namespace WS\Site\Library\Metadata;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

trait MetadataFormTrait
{
    protected function addMetadataFieldsFields(FormBuilderInterface $builder): void
    {
        $builder
            ->add('metadataTitle', TextType::class, [
                'label' => 'metadata.fields.metadataTitle.label',
                'translation_domain' => 'ws_cms_site_seo',
            ])
            ->add('metadataDescription', TextType::class, [
                'label' => 'metadata.fields.metadataDescription.label',
                'translation_domain' => 'ws_cms_site_seo',
            ])
            ->add('metadataKeywords', TextType::class, [
                'label' => 'metadata.fields.metadataKeywords.label',
                'translation_domain' => 'ws_cms_site_seo',
            ])
        ;
    }
}
