<?php

namespace WebEtDesign\ActualityBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="WebEtDesign\ActualityBundle\Repository\CategoryRepository")
 * @ORM\Table(name="actuality__category")
 */
class Category
{
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
     * @var null|string
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\SortablePosition
     */
    private ?int $position = null;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="WebEtDesign\ActualityBundle\Entity\Actuality", mappedBy="category")
     */
    private Collection $actualities;

    public function __construct()
    {
        $this->actualities = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getTitle();
    }

    public function countPublishedActuality()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('published', true))
            ->andWhere(Criteria::expr()->lt('publishedAt', new DateTime('now')));

        return $this->actualities->matching($criteria)->count();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|Actuality[]
     */
    public function getActualities(): Collection
    {
        return $this->actualities;
    }

    public function addActuality(Actuality $actuality): self
    {
        if (!$this->actualities->contains($actuality)) {
            $this->actualities[] = $actuality;
            $actuality->setCategory($this);
        }

        return $this;
    }

    public function removeActuality(Actuality $actuality): self
    {
        if ($this->actualities->contains($actuality)) {
            $this->actualities->removeElement($actuality);
            // set the owning side to null (unless already changed)
            if ($actuality->getCategory() === $this) {
                $actuality->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @param string|null $slug
     * @return Category
     */
    public function setSlug(?string $slug): Category
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

}
