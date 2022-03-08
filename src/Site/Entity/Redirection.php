<?php

namespace WS\Site\Entity;

use WS\Core\Entity\Domain;
use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="WS\Site\Repository\RedirectionRepository")
 * @ORM\Table(name="ws_site_redirection", indexes={@ORM\Index(name="ws_idx_site_redirection_search", columns={"redirection_domain", "redirection_origin"})})
 */
class Redirection
{
    use TimestampableTrait;
    use BlameableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="redirection_id", type="integer")
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="WS\Core\Entity\Domain")
     * @ORM\JoinColumn(name="redirection_domain", referencedColumnName="domain_id", nullable=true)
     */
    private ?Domain $domain = null;

    /**
     * @Assert\Length(max=190)
     * @ORM\Column(name="redirection_origin", type="string", length=190, nullable=false)
     */
    private string $origin;

    /**
     * @Assert\Length(max=256)
     * @ORM\Column(name="redirection_destination", type="string", length=256, nullable=false)
     */
    private string $destination;

    /**
     * @ORM\Column(name="redirection_exact_match", type="boolean", nullable=false)
     */
    private bool $exactMatch = false;

    /**
     * @Assert\Length(max=128)
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="redirection_created_by", type="string", length=128, nullable=true)
     */
    private ?string $createdBy = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="redirection_created_at", type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Assert\Type("DateTime")
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="redirection_modified_at", type="datetime")
     */
    private \DateTimeInterface $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setDestination(?string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function isExactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function setExactMatch(bool $exactMatch): self
    {
        $this->exactMatch = $exactMatch;

        return $this;
    }

    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain(): ?Domain
    {
        return $this->domain;
    }
}
