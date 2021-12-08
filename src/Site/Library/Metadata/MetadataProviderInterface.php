<?php

namespace WS\Site\Library\Metadata;

interface MetadataProviderInterface
{
    const OPEN_GRAPH_TYPE_WEBSITE = 'website';
    const OPEN_GRAPH_TYPE_ARTICLE = 'article';

    public function getMetadataSupportFor(): array;

    public function getMetadataTitle(object $element): ?string;

    public function getMetadataDescription(object $element): ?string;

    public function getMetadataKeywords(object $element): ?string;

    public function getOpenGraphTitle(object $element): ?string;

    public function getOpenGraphType(object $element): ?string;

    public function getOpenGraphImage(object $element): ?string;

    public function getOpenGraphImageWidth(object $element): ?int;

    public function getOpenGraphImageHeight(object  $element): ?int;
}
