<?php

namespace App\Entity\Actuality;

use App\Repository\Actuality\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use WebEtDesign\ActualityBundle\Entity\WDCategory;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table(name="actuality__category")
 * @UniqueEntity(fields={"title"}, message="form.title.error.already_exist")
 */
class Category extends WDCategory
{

}   