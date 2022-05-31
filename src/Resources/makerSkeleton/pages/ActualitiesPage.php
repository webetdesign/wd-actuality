<?php

namespace App\CMS\Page\Actuality;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WebEtDesign\ActualityBundle\CMS\Page\WDActualitiesPage;
use WebEtDesign\CmsBundle\Attribute\AsCmsPage;

#[AsCmsPage(code: self::code)]
class ActualitiesPage extends WDActualitiesPage
{
    
}