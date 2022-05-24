<?php

namespace WebEtDesign\ActualityBundle\CMS\Page;

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

abstract class WDActualitiesPage extends AbstractPage
{
    public const code = "ACTUALITIES";
    public const routeName = "category_actuality";

    protected ?string $template = 'pages/actuality/list.html.twig';

    protected ?string $label = 'List des actualités';

    public function getRoute(): ?RouteDefinition
    {
        return RouteDefinition::new()
            ->setController(ActualityController::class)
            ->setAction('list')
            ->setPath('/actualite/{category}')
            ->setName( self::routeName)
            ->setAttributes([
                RouteAttributeDefinition::new('category')
                    ->setEntityClass(Category::class)
                    ->setEntityProperty('slug')
                    ->setDefault(null)
            ]);
    }
}
