<?php

namespace WS\Site\Twig\Tag\MetaTags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class MetaTagsNode extends Node
{
    public function __construct(string $name, AbstractExpression $value, int $line = 0, string $tag = null)
    {
        parent::__construct(['value' => $value], ['name' => $name], $line, $tag);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('$this->env->getExtension(\'WS\Site\Twig\Extension\MetadataExtension\')->configure(')
             ->subcompile($this->getNode('value'))
             ->raw(');');
    }
}
