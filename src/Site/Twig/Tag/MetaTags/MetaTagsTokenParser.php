<?php

namespace WS\Site\Twig\Tag\MetaTags;

use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class MetaTagsTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): MetaTagsNode
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);

        $name = 'metatags_configuration';
        return new MetaTagsNode($name, $value, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'metatags_configuration';
    }
}
