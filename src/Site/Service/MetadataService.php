<?php

namespace WS\Site\Service;

use WS\Site\Library\Metadata\MetadataProviderInterface;

class MetadataService
{
    protected array $providers;

    public function registerProvider(MetadataProviderInterface $provider)
    {
        foreach ($provider->getMetadataSupportFor() as $supported) {
            $this->providers[$supported] = $provider;
        }
    }

    public function getTitle($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataTitle($element);
        }

        return null;
    }

    public function getDescription($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataDescription($element);
        }

        return null;
    }

    public function getKeywords($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getMetadataKeywords($element);
        }

        return null;
    }

    public function getOpenGraphTitle($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphTitle($element);
        }

        return null;
    }

    public function getOpenGraphType($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphType($element);
        }

        return null;
    }

    public function getOpenGraphImage($element): ?string
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImage($element);
        }

        return null;
    }

    public function getOpenGraphImageWidth($element): ?int
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImageWidth($element);
        }

        return null;
    }

    public function getOpenGraphImageHeight($element): ?int
    {
        if ($this->hasProvider($element)) {
            $provider = $this->getProvider($element);
            return $provider->getOpenGraphImageHeight($element);
        }

        return null;
    }

    protected function hasProvider($element): bool
    {
        if (is_object($element)) {
            return array_key_exists(get_class($element), $this->providers);
        } elseif (is_string($element)) {
            return array_key_exists($element, $this->providers);
        }

        return false;
    }

    protected function getProvider($element): MetadataProviderInterface
    {
        return $this->providers[get_class($element)];
    }
}
