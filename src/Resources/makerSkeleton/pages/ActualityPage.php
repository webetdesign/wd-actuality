<?php

namespace App\CMS\Page\Actuality;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WebEtDesign\ActualityBundle\CMS\Page\WDActualityPage;
use WebEtDesign\CmsBundle\Attribute\AsCmsPage;

#[AsCmsPage(code: self::code)]
class ActualityPage extends WDActualityPage
{
    
}