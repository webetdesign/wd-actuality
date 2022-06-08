<?php

namespace WebEtDesign\ActualityBundle\Entity;

use App\Entity\Actuality\Actuality;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PropertyAccess\PropertyAccess;
use WebEtDesign\RgpdBundle\Annotations\Exportable;

/**
 * @ORM\MappedSuperclass()
 * @method string setSlug(?string $str)
 * @method string getTitle()
 * @method string setTitle(?string $str)
 */
abstract class WDCategory implements TranslatableInterface
{
    use TimestampableEntity;
    use TranslatableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\SortablePosition
     */
    protected ?int $position = null;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Actuality", mappedBy="category")
     */
    protected Collection $actualities;

    public function __construct()
    {
        $this->actualities = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getTitle();
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
}
