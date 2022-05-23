<?php

namespace WebEtDesign\ActualityBundle\Entity;

use App\Entity\Actuality\Actuality;
use Doctrine\ORM\Mapping as ORM;
use WebEtDesign\MediaBundle\Entity\Media;

/**
 * @ORM\MappedSuperclass()
 */
class WDActualityMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $position;

    /**
     * @ORM\ManyToOne(targetEntity=Actuality::class, inversedBy="actualityMedia"))
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?Actuality $actuality = null;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class,cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     */
    protected Media $media;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getActuality(): ?Actuality
    {
        return $this->actuality;
    }

    public function setActuality(?Actuality $actuality): self
    {
        $this->actuality = $actuality;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }
}