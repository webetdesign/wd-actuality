<?php

namespace App\Entity\Actuality;

use App\Repository\Actuality\ActualityRepository;
use Doctrine\ORM\Mapping as ORM;
use WebEtDesign\ActualityBundle\Entity\WDActuality;

/**
 * @ORM\Entity(repositoryClass=ActualityRepository::class)
 * @ORM\Table(name="actuality__actuality")
 */
class Actuality extends WDActuality
{

}