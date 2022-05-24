<?php

namespace WebEtDesign\ActualityBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use WebEtDesign\MediaBundle\Entity\Media;
use WebEtDesign\SeoBundle\Entity\SeoAwareTrait;
use WebEtDesign\SeoBundle\Entity\SmoOpenGraphTrait;
use WebEtDesign\SeoBundle\Entity\SmoTwitterTrait;

/**
 * @ORM\Entity(repositoryClass="WebEtDesign\ActualityBundle\Repository\ActualityRepository")
 * @ORM\Table(name="actuality__actuality")
 */
class Actuality
{

    use SeoAwareTrait;
    use SmoOpenGraphTrait;
    use SmoTwitterTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private ?string $slug = null;

    /**
     * @var null|Media
     *
     * @ORM\ManyToOne(targetEntity="WebEtDesign\MediaBundle\Entity\Media")
     */
    private ?Media $picture = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $excerpt = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $publishedAt;

    /**
     * @var null|Category
     * @ORM\ManyToOne(targetEntity="WebEtDesign\ActualityBundle\Entity\Category", inversedBy="actualities")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
<<<<<<< Updated upstream:src/Entity/Actuality.php
    private ?Category $category;
=======
    protected ?Category $category = null;

    /**
     * @ORM\OneToMany(targetEntity=ActualityMedia::class, mappedBy="actuality", cascade={"persist", "remove"}))
     * @OrderBy({"position" = "ASC"})
     */
    protected Collection $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }
>>>>>>> Stashed changes:src/Entity/WDActuality.php

    public function __toString()
    {
        return (string) $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPicture(): ?Media
    {
        return $this->picture;
    }

    public function setPicture(?Media $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
<<<<<<< Updated upstream:src/Entity/Actuality.php
=======

    /**
     * @return Collection<int, ActualityMedia>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(ActualityMedia $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setActuality($this);
        }

        return $this;
    }

    public function removePicture(ActualityMedia $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getActuality() === $this) {
                $picture->setActuality(null);
            }
        }

        return $this;
    }
>>>>>>> Stashed changes:src/Entity/WDActuality.php
}
