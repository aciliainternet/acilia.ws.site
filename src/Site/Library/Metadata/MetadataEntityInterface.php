<?php

namespace WS\Site\Library\Metadata;

interface MetadataEntityInterface
{
    public function getMetadataTitle(): ?string;

    public function getMetadataDescription(): ?string;

    public function getMetadataKeywords(): ?string;
}
