<?php

namespace WS\Site\Entity;

use WS\Core\Library\Domain\DomainDependantInterface;
use WS\Core\Library\Domain\DomainDependantTrait;
use WS\Core\Library\Publishing\PublishingEntityInterface;
use WS\Core\Library\Publishing\PublishingEntityTrait;
use WS\Core\Library\Traits\Entity\BlameableTrait;
use WS\Core\Library\Traits\Entity\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use WS\Core\Entity\Domain;

/**
 * @ORM\Entity(repositoryClass="WS\Site\Repository\WidgetConfigurationRepository")
 * @ORM\Table(name="ws_site_widget_configuration")
 */

class WidgetConfiguration implements DomainDependantInterface, PublishingEntityInterface
{
    use DomainDependantTrait;
    use PublishingEntityTrait;
    use BlameableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="config_id", type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="WS\Core\Entity\Domain")
     * @ORM\JoinColumn(name="config_domain", referencedColumnName="domain_id", nullable=false)
     */
    private Domain $domain;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     *
     * @ORM\Column(name="config_name", type="string", length=64, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="config_widget", type="string", length=64, nullable=false)
     */
    private string $widget;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     *
     * @ORM\Column(name="config_code", type="string", length=64, nullable=false)
     */
    private string $code;

    /**
     * @ORM\Column(name="config_configuration", type="json", nullable=false)
     */
    private array $configuration;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=32)
     *
     * @ORM\Column(name="config_publish_status", type="string", length=32, nullable=false)
     */
    private string $publishStatus;

    /**
     * @ORM\Column(name="config_publish_since", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $publishSince = null;

    /**
     * @ORM\Column(name="config_publish_until", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $publishUntil = null;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="config_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="config_modified_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $modifiedAt;

    /**
     * @Assert\Length(max=128)
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="config_created_by", type="string", length=128, nullable=true)
     */
    private ?string $createdBy = null;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setWidget(?string $widget): self
    {
        $this->widget = $widget;

        return $this;
    }

    public function getWidget(): ?string
    {
        return $this->widget;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setConfiguration(?array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }
}
