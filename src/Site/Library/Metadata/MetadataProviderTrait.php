<?php

namespace WS\Site\Library\Metadata;

use WS\Core\Library\CRUD\AbstractService;

trait MetadataProviderTrait
{
    public function getMetadataSupportFor(): array
    {
        if ($this instanceof AbstractService) {
            return [$this->getEntityClass()];
        }

        return [];
    }

    public function getMetadataTitle($element): ?string
    {
        if ($element instanceof MetadataEntityInterface) {
            if ($element->getMetadataTitle()) {
                return $element->getMetadataTitle();
            }
        }

        if (method_exists($element, 'getTitle')) {
            return $element->getTitle();
        }

        if (method_exists($element, 'getSubtitle')) {
            return $element->getSubtitle();
        }

        return null;
    }

    public function getMetadataDescription($element): ?string
    {
        if ($element instanceof MetadataEntityInterface) {
            if ($element->getMetadataDescription()) {
                return $element->getMetadataDescription();
            }
        }

        if (method_exists($element, 'getDescription')) {
            return $element->getDescription();
        }

        return null;
    }

    public function getMetadataKeywords($element): ?string
    {
        if ($element instanceof MetadataEntityInterface) {
            if ($element->getMetadataKeywords()) {
                return $element->getMetadataKeywords();
            }
        }

        return null;
    }

    public function getOpenGraphTitle($element): ?string
    {
        return $this->getMetadataTitle($element);
    }

    public function getOpenGraphType($element): ?string
    {
        return MetadataProviderInterface::OPEN_GRAPH_TYPE_WEBSITE;
    }

    public function getOpenGraphImage($element): ?string
    {
        return null;
    }

    public function getOpenGraphImageWidth($element): ?int
    {
        return null;
    }

    public function getOpenGraphImageHeight($element): ?int
    {
        return null;
    }
}
