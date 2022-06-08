<?php

namespace WebEtDesign\ActualityBundle\Entity;

use App\Entity\Actuality\Category;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use WebEtDesign\MediaBundle\Entity\Media;
use WebEtDesign\RgpdBundle\Annotations\Exportable;
use WebEtDesign\SeoBundle\Entity\SeoAwareTrait;
use WebEtDesign\SeoBundle\Entity\SmoOpenGraphTrait;
use WebEtDesign\SeoBundle\Entity\SmoTwitterTrait;
use App\Entity\Actuality\ActualityMedia;

/**
 * @ORM\MappedSuperclass()
 * @method string getTitle()
 * @method string setTitle(?string $str)
 * @method string getExcerpt()
 * @method string setExcerpt(?string $str)
 * @method string getContent()
 * @method string setContent(?string $str)
 * @method string setSlug(?string $str)
 */
abstract class WDActuality implements TranslatableInterface
{
    use TimestampableEntity;
    use SeoAwareTrait;
    use SmoOpenGraphTrait;
    use SmoTwitterTrait;
    use TranslatableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @var null|Media
     *
     * @ORM\ManyToOne(targetEntity="WebEtDesign\MediaBundle\Entity\Media")
     */
    protected ?Media $thumbnail = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected ?bool $published = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $publishedAt = null;

    /**
     * @var null|Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="actualities")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
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

    public function __toString()
    {
        return (string) $this->translate($this->getCurrentLocale())->getTitle();
    }

    public function __call($method, $arguments)
    {
        if ($method == '_action') {
            return null;
        }

        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
    }

    public function getSlug()
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), 'getSlug');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    // Getter and setter for split input in few tabs in admin form
    public function getTranslationsTitle()
    {
        return $this->getTranslations();
    }

    public function setTranslationsTitle(iterable $translations): void
    {
        $this->ensureIsIterableOrCollection($translations);

        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }
    }

    public function getTranslationsContent()
    {
        return $this->getTranslations();
    }

    public function setTranslationsContent(iterable $translations): void
    {
        $this->ensureIsIterableOrCollection($translations);

        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }
    }
}
