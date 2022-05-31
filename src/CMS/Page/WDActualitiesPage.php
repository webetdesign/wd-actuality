<?php

namespace WebEtDesign\ActualityBundle\CMS\Page;

use App\Entity\Actuality\Category;
use App\Entity\User\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    public const routeName = "actualities";

    protected ?string $template = 'pages/actuality/actualities.html.twig';

    protected ?string $label = 'Listing des actualités';

    protected bool $useCategory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
    }
    
    public function getRoute(): ?RouteDefinition
    {
        $routeDefinition =  RouteDefinition::new()
            ->setController(ActualityController::class)
            ->setAction('list')
            ->setName( self::routeName);

        if ($this->useCategory) {
            $routeDefinition
                ->setAttributes([
                    RouteAttributeDefinition::new('category')
                        ->setEntityClass(Category::class)
                        ->setEntityProperty('slug')
                        ->setDefault(null)
                ])
                ->setPath('/actualite/{category}');
        }else{
            $routeDefinition->setPath('/actualite');
        }
        
        return $routeDefinition;
    }
}
