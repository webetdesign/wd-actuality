<?php

namespace WebEtDesign\ActualityBundle\Entity;

use App\Entity\Actuality\Category;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use WebEtDesign\MediaBundle\Entity\Media;
use WebEtDesign\RgpdBundle\Annotations\Exportable;
use WebEtDesign\SeoBundle\Entity\SeoAwareTrait;
use WebEtDesign\SeoBundle\Entity\SmoOpenGraphTrait;
use WebEtDesign\SeoBundle\Entity\SmoTwitterTrait;

/**
 * @ORM\MappedSuperclass()
 */
abstract class WDActuality
{
    use TimestampableEntity;
    use SeoAwareTrait;
    use SmoOpenGraphTrait;
    use SmoTwitterTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $title = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    protected ?string $slug = null;

    /**
     * @var null|Media
     *
     * @ORM\ManyToOne(targetEntity="WebEtDesign\MediaBundle\Entity\Media")
     */
    protected ?Media $thumbnail = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $excerpt = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $content = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $published = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $draft = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $publishedAt = null;

    /**
     * @var null|Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="actualities")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected ?Category $category = null;

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

    public function getThumbnail(): ?Media
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?Media $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

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

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->draft;
    }

    /**
     * @param bool $draft
     * @return WDActuality
     */
    public function setDraft(bool $draft): self
    {
        $this->draft = $draft;

        return $this;
    }
}
