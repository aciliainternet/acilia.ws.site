<?php

namespace WS\Site\Service;

class MetaTagsService
{
    protected MarkdownService $markdownService;
    protected array $config;
    protected array $custom;

    public function __construct(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;

        $this->config = [
                0 => [
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                    'og_image' => '',
                    'og_image_width' => '',
                    'og_image_height' => '',
                    'og_type' => '',
                    'og_video' => '',
                    'og_video_secure_url' => '',
                    'og_video_width' => '',
                    'og_video_height' => '',
                    'og_video_type' => ''
                ]
            ];

        $this->custom = [];
    }

    public function setCustom(string $tag, string $value): void
    {
        $this->custom[$tag] = $value;
    }

    public function setCustoms(array $tags): void
    {
        foreach ($tags as $tag => $value) {
            $this->setCustom($tag, $value);
        }
    }

    public function getCustomTags(): array
    {
        return $this->custom;
    }

    public function configure(array $configuration): void
    {
        $order = (isset($configuration['order'])) ? $configuration['order'] : 0;

        foreach ($configuration as $key => $value) {
            if ($value !== '' && $key !== 'order') {
                if (!isset($this->config[$order][$key]) || !$this->config[$order][$key]) {
                    $this->config[$order][$key] = $value;
                }
            }
        }
    }

    public function compileConfiguration(): array
    {
        krsort($this->config);
        $config = [];

        foreach ($this->config as $c) {
            foreach ($c as $key => $value) {
                switch ($key) {
                    case 'title':
                        if (isset($config['title'])) {
                            $config['title'] .= ' | ' . $value;
                        } else {
                            $config['title'] = $value;
                        }
                        break;

                    case 'og_title':
                        if (isset($config['og_title'])) {
                            $config['og_title'] .= ' | ' . $value;
                        } else {
                            $config['og_title'] = $value;
                        }
                        break;

                    case 'description':
                        if (isset($config['description'])) {
                            if (strlen($config['description']) < 100) {
                                $config['description'] .= ' - ' . $value;
                            }
                        } else {
                            $config['description'] = $value;
                        }
                        break;
                    case 'keywords':
                        if (isset($config['keywords'])) {
                            if (strlen($config['keywords']) < 150) {
                                $config['keywords'] .= ', ' . $value;
                            }
                        } else {
                            $config['keywords'] = $value;
                        }
                        break;
                    case 'order':
                        break;
                    default:
                        if (!isset($config[$key]) || trim($value)) {
                            $config[$key] = trim($value);
                        }
                        break;
                }
            }
        }

        $config['title'] = $this->sanitize($config['title']);
        $config['description'] = $this->sanitize($config['description']);
        $config['keywords'] = $this->sanitize($config['keywords']);
        $config['og_title'] = isset($config['og_title']) ? $this->sanitize($config['og_title']) : $this->sanitize($config['title']);

        return $config;
    }

    protected function sanitize(string $content): ?string
    {
        $content = $this->markdownService->parse($content);
        $content = strip_tags($content);
        $content = trim($content);

        return $content;
    }
}
