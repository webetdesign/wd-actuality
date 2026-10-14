<?php

namespace WebEtDesign\ActualityBundle\Entity;

use A2lix\TranslationFormBundle\Helper\OneLocaleInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\MappedSuperclass]
abstract class WDCategoryTranslation implements TranslationInterface, OneLocaleInterface
{
    use TranslationTrait;

    /** A2lix 4 writes the locale directly; Knp stores it as a protected property. */
    public function __set(string $name, mixed $value): void
    {
        if ('locale' !== $name) {
            throw new \LogicException(sprintf('Cannot set inaccessible property %s::$%s.', static::class, $name));
        }
        $this->setLocale($value);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $title = null;

    /** @var null|string */
    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    protected ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
