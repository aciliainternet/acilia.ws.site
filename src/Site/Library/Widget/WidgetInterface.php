<?php

namespace WS\Site\Library\Widget;

use Symfony\Component\Form\FormBuilderInterface;
use WS\Site\Entity\WidgetConfiguration;

interface WidgetInterface
{
    public function getId() : string;

    public function getIcon() : string;

    public function getDefinition(FormBuilderInterface $builder) : void;

    public function getTemplate(WidgetConfiguration $configuration) : string;

    public function getData(WidgetConfiguration $configuration) : array;

    public function getTranslationDomain() : string;

    public function getImageFields() : array;
}
