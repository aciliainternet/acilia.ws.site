<?php

namespace WS\Site\Service;

use WS\Site\Library\Metadata\MetadataProviderInterface;

class MetadataService
{
    protected array $providers;

    public function registerProvider(MetadataProviderInterface $provider): void
    {
        foreach ($provider->getMetadataSupportFor() as $supported) {
            $this->providers[$supported] = $provider;
        }
    }

    public function getTitle(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataTitle($element);
        }

        return null;
    }

    public function getDescription(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataDescription($element);
        }

        return null;
    }

    public function getKeywords(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataKeywords($element);
        }

        return null;
    }

    public function getOpenGraphTitle(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphTitle($element);
        }

        return null;
    }

    public function getOpenGraphType(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphType($element);
        }

        return null;
    }

    public function getOpenGraphImage(object $element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImage($element);
        }

        return null;
    }

    public function getOpenGraphImageWidth(object $element): ?int
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImageWidth($element);
        }

        return null;
    }

    public function getOpenGraphImageHeight(object $element): ?int
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImageHeight($element);
        }

        return null;
    }

    protected function hasProvider(object $element): bool
    {
        return array_key_exists(get_class($element), $this->providers);
    }

    protected function getProvider(object $element): MetadataProviderInterface
    {
        return $this->providers[get_class($element)];
    }
}
