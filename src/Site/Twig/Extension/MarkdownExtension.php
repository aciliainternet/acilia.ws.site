<?php

namespace WS\Site\Twig\Extension;

use WS\Site\Service\MarkdownService as ParserService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    private ParserService $parser;

    public function __construct(ParserService $parser)
    {
        $this->parser = $parser;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown($markdown): mixed
    {
        return $this->parser->parse($markdown);
    }
}
