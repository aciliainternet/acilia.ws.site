<?php

namespace WS\Site\Library\Sitemap;

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

    protected $url;
    protected $lastModified;
    protected $frequency;
    protected $priority;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param \DateTime $lastModified
     * @return Url
     */
    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return Url
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param integer $priority
     * @return Url
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    public function getXML(\DOMDocument $root, \DOMElement $urlset)
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
