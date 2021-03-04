<?php

namespace WS\Site\Library\Metadata;

use WS\Site\Service\MetadataService;
use WS\Site\Service\MetaTagsService;
use WS\Site\Service\MetadataConsumerService;

trait MetadataConsumerTrait
{
    /** @var MetadataConsumerService */
    protected $metadataConsumerService;

    public function setMetadataConsumerService(MetadataConsumerService $metadataConsumerService)
    {
        $this->metadataConsumerService = $metadataConsumerService;
    }

    public function getMetadataService(): MetadataService
    {
        return $this->metadataConsumerService->getMetadataService();
    }

    public function getMetaTagsService(): MetaTagsService
    {
        return $this->metadataConsumerService->getMetaTagsService();
    }

    public function configureMetadata($element, $order = 10)
    {
        $configuration = [
            'order' => $order,
            'title' => $this->getMetadataService()->getTitle($element),
            'description' => $this->getMetadataService()->getDescription($element),
            'keywords' => $this->getMetadataService()->getKeywords($element),
            'og_title' => $this->getMetadataService()->getOpenGraphTitle($element),
            'og_type' => $this->getMetadataService()->getOpenGraphType($element),
            'og_image' => $this->getMetadataService()->getOpenGraphImage($element),
            'og_image_width' => $this->getMetadataService()->getOpenGraphImageWidth($element),
            'og_image_height' => $this->getMetadataService()->getOpenGraphImageHeight($element),
        ];

        $this->getMetaTagsService()->configure($configuration);
    }
}
