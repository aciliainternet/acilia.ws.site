<?php

namespace WS\Site\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RedirectionExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ws_redirection_exact_match_format', [$this, 'redirectionExactMatchFormat'], ['is_safe' => ['html']]),
        ];
    }

    public function redirectionExactMatchFormat(bool $value): string
    {
        $mapping = [
            true => ['badge' => 'success', 'label' => 'form.exactMatch.option.yes.label'],
            false => ['badge' => 'secondary', 'label' => 'form.exactMatch.option.no.label'],
        ];

        return sprintf(
            '<span class="c-badge c-badge--%s">%s</span>',
            $mapping[$value]['badge'],
            $this->translator->trans($mapping[$value]['label'], [], 'ws_cms_site_redirection')
        );
    }
}
