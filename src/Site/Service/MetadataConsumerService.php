<?php

namespace WS\Site\Service;

class MetadataConsumerService
{
    protected $metadataService;
    protected $metaTagsService;

    public function __construct(MetadataService $metadataService, MetaTagsService $metaTagsService)
    {
        $this->metadataService = $metadataService;
        $this->metaTagsService = $metaTagsService;
    }

    public function getMetadataService(): MetadataService
    {
        return $this->metadataService;
    }

    public function getMetaTagsService(): MetaTagsService
    {
        return $this->metaTagsService;
    }
}