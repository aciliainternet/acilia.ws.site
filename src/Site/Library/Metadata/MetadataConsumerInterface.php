<?php

namespace WS\Site\Library\Metadata;

use WS\Site\Service\MetadataService;
use WS\Site\Service\MetaTagsService;
use WS\Site\Service\MetadataConsumerService;

interface MetadataConsumerInterface
{
    public function setMetadataConsumerService(MetadataConsumerService $metadataConsumerService): void;

    public function getMetadataService(): MetadataService;

    public function getMetaTagsService(): MetaTagsService;

    public function configureMetadata(object $element): void;
}
