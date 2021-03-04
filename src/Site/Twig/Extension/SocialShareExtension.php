<?php

namespace WS\Site\Twig\Extension;

use WS\Core\Service\SettingService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SocialShareExtension extends AbstractExtension
{
    protected $settingService;
    protected $translator;

    public function __construct(SettingService $settingService, TranslatorInterface $translator)
    {
        $this->settingService = $settingService;
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('twitter_link', [$this, 'getTwitterLink']),
            new TwigFunction('facebook_link', [$this, 'getFacebookLink']),
            new TwigFunction('email_link', [$this, 'getEmailLink']),
        ];
    }

    public function getTwitterLink($path, $text = '')
    {
        $twitterName = $this->settingService->get('social_twitter_profile');

        return sprintf(
            'https://twitter.com/intent/tweet?screen_name=%s&ref_src=%s&url=%s&text=%s',
            $twitterName,
            $path,
            $path,
            $text
        );
    }

    public function getFacebookLink($path)
    {
        return sprintf('https://www.facebook.com/sharer/sharer.php?u=%s&amp;src=sdkpreparse', $path);
    }

    public function getEmailLink($title, $path)
    {
        return sprintf(
            'mailto:?subject=%s%s&amp;body=%s %s',
            $this->translator->trans('blog.share_email_title'),
            $title,
            $this->translator->trans('blog.share_email_description'),
            $path
        );
    }
}
