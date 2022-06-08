<?php

namespace App\Entity\Actuality;

use App\Repository\Actuality\ActualityTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use WebEtDesign\ActualityBundle\Entity\WDActualityTranslation;

/**
 * @ORM\Entity(repositoryClass=ActualityTranslationRepository::class)
 * @ORM\Table(name="actuality__actuality_translation")
 */
class ActualityTranslation extends WDActualityTranslation
{
}
