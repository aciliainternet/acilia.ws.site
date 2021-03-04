<?php

namespace WS\Site\Library\Metadata;

interface MetadataProviderInterface
{
    const OPEN_GRAPH_TYPE_WEBSITE = 'website';
    const OPEN_GRAPH_TYPE_ARTICLE = 'article';

    public function getMetadataSupportFor(): array;

    public function getMetadataTitle($element): ?string;

    public function getMetadataDescription($element): ?string;

    public function getMetadataKeywords($element): ?string;

    public function getOpenGraphTitle($element): ?string;

    public function getOpenGraphType($element): ?string;

    public function getOpenGraphImage($element): ?string;

    public function getOpenGraphImageWidth($element): ?int;

    public function getOpenGraphImageHeight($element): ?int;
}
