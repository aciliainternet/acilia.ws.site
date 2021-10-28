<?php

namespace WS\Site\Service;

use Parsedown;

class MarkdownService
{
    private Parsedown $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse($markdown): string
    {
        return $this->parser->parse($markdown);
    }
}
