<?php

namespace WS\Site\Service;

use Parsedown;

class MarkdownService
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse($markdown)
    {
        return $this->parser->parse($markdown);
    }
}
