<?php

namespace WS\Site\Twig\Extension;


use WS\Site\Service\MetadataConsumerService;
use WS\Site\Twig\Tag\MetaTags\MetaTagsTokenParser;
use Twig\TwigFunction;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

class MetadataExtension extends AbstractExtension
{
    protected MetadataConsumerService $metadataConsumerService;

    public function __construct(MetadataConsumerService $metadataConsumerService)
    {
        $this->metadataConsumerService = $metadataConsumerService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_metadata_title', [$this, 'getTitle'], ['is_safe' => ['html']]),
            new TwigFunction('get_metadata_description', [$this, 'getDescription'], ['is_safe' => ['html']]),
            new TwigFunction('get_metadata_keywords', [$this, 'getKeywords'], ['is_safe' => ['html']]),
            new TwigFunction('get_metadata_og_title', [$this, 'getOpenGraphTitle'], ['is_safe' => ['html']]),
            new TwigFunction('get_metadata_og_type', [$this, 'getOpenGraphType'], ['is_safe' => ['html']]),
            new TwigFunction('get_metadata_og_image', [$this, 'getOpengraphImage'], ['is_safe' => ['html']]),
            new TwigFunction('render_metatags', [$this, 'renderMetaTags'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function getTitle($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getTitle($entity);
    }

    public function getDescription($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getDescription($entity);
    }

    public function getKeywords($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getKeywords($entity);
    }

    public function getOpenGraphTitle($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getOpenGraphTitle($entity);
    }

    public function getOpenGraphType($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getOpenGraphType($entity);
    }

    public function getOpengraphImage($entity): ?string
    {
        return $this->metadataConsumerService->getMetadataService()->getOpengraphImage($entity);
    }

    public function getTokenParsers(): array
    {
        return [
            new MetaTagsTokenParser(),
        ];
    }

    public function renderMetaTags(Environment $environment): string
    {
        $config = $this->metadataConsumerService->getMetaTagsService()->compileConfiguration();
        $customTags = $this->metadataConsumerService->getMetaTagsService()->getCustomTags();

        try {
            return $environment->render('@WSSite/site/metatags/index.html.twig', [
                'config' => $config,
                'custom' => $customTags
            ]);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function configure($configuration): void
    {
        $this->metadataConsumerService->getMetaTagsService()->configure($configuration);
    }
}
