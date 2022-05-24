<?php

namespace WebEtDesign\ActualityBundle\CMS\Page;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
use App\Entity\User\User;
use WebEtDesign\ActualityBundle\Controller\ActualityController;
use WebEtDesign\CmsBundle\Attribute\AsCmsPage;
use WebEtDesign\CmsBundle\CmsBlock\CheckboxBlock;
use WebEtDesign\CmsBundle\CmsBlock\ChoiceBlock;
use WebEtDesign\CmsBundle\CmsBlock\EntityBlock;
use WebEtDesign\CmsBundle\CmsBlock\StaticBlock;
use WebEtDesign\CmsBundle\CmsBlock\TextareaBlock;
use WebEtDesign\CmsBundle\CmsBlock\TextBlock;
use WebEtDesign\CmsBundle\CmsBlock\WysiwygBlock;
use WebEtDesign\CmsBundle\CmsTemplate\AbstractPage;
use WebEtDesign\CmsBundle\DependencyInjection\Models\BlockDefinition;
use WebEtDesign\CmsBundle\DependencyInjection\Models\RouteAttributeDefinition;
use WebEtDesign\CmsBundle\DependencyInjection\Models\RouteDefinition;
use WebEtDesign\MediaBundle\Blocks\MediaBlock;


abstract class WDActualityPage extends AbstractPage
{
    public const code = "ACTUALITY";
    public const routeName = "actuality";

    protected ?string $template = 'pages/actuality/actuality.html.twig';

    protected ?string $label = 'Page actualité';

    public function getRoute(): ?RouteDefinition
    {
        return RouteDefinition::new()
            ->setController(ActualityController::class)
            ->setPath('/actualite/{category}/{actuality}')
            ->setName( self::routeName)
            ->setAttributes([
                RouteAttributeDefinition::new('category')
                    ->setEntityClass(Category::class)
                    ->setEntityProperty('slug'),
                RouteAttributeDefinition::new('page')
                    ->setEntityClass(Actuality::class)
                    ->setEntityProperty('slug'),
            ]);
    }
}
