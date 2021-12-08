<?php

namespace WS\Site\Service;

class MarkdownService
{
    private \Parsedown $parser;

    public function __construct()
    {
        $this->parser = new \Parsedown();
    }

    public function parse(string $markdown): string
    {
        return \strval($this->parser->parse($markdown));
    }
}
