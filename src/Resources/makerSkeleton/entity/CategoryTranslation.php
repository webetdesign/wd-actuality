<?php

namespace App\Entity\Actuality;

use App\Repository\Actuality\CategoryTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use WebEtDesign\ActualityBundle\Entity\WDActualityTranslation;
use WebEtDesign\ActualityBundle\Entity\WDCategoryTranslation;

/**
 * @ORM\Entity(repositoryClass=CategoryTranslationRepository::class)
 * @ORM\Table(name="actuality__category_translation")
 */
class CategoryTranslation extends WDCategoryTranslation
{

}
