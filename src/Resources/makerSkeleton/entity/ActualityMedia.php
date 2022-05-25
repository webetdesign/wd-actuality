<?php

namespace App\Entity\Actuality;

use App\Repository\Actuality\ActualityMediaRepository;
use Doctrine\ORM\Mapping as ORM;
use WebEtDesign\ActualityBundle\Entity\WDActualityMedia;

/**
 * @ORM\Entity(repositoryClass=ActualityMediaRepository::class)
 * @ORM\Table(name="actuality__actuality_media")
 */
class ActualityMedia extends WDActualityMedia
{

}