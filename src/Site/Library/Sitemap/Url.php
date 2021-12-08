<?php

namespace WS\Site\Library\Sitemap;

use DOMElement;

class Url
{
    const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    const FREQUENCY_ALWAYS  = 'always';
    const FREQUENCY_HOURLY  = 'hourly';
    const FREQUENCY_DAILY   = 'daily';
    const FREQUENCY_WEEKLY  = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_YEARLY  = 'yearly';
    const FREQUENCY_NEVER   = 'never';

    protected string $url;
    protected \DateTime $lastModified;
    protected string $frequency;
    protected float $priority;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    public function setLastModified(\DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getPriority(): float
    {
        return $this->priority;
    }

    public function setPriority(float $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getXML(\DOMDocument $root, \DOMElement $urlset): DOMElement
    {
        $urlset->setAttributeNS('', 'xmlns', self::SCHEMA);

        $url = $root->createElement('url');
        $url->appendChild(new \DOMElement('loc', htmlspecialchars($this->getUrl())));
        if ($this->getLastModified() instanceof \DateTime) {
            $url->appendChild(new \DOMElement('lastmod', $this->getLastModified()->format('Y-m-d')));
        }
        $url->appendChild(new \DOMElement('changefreq', $this->getFrequency()));
        $url->appendChild(new \DOMElement('priority', number_format($this->getPriority(), 1, '.', '')));

        return $url;
    }
}
