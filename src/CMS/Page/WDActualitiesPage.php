<?php

namespace WebEtDesign\ActualityBundle\CMS\Page;

use App\Entity\Actuality\Category;
use App\Entity\User\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WebEtDesign\ActualityBundle\Controller\ActualityController;
use WebEtDesign\CmsBundle\Attribute\AsCmsPage;
use WebEtDesign\CmsBundle\CMS\Block\CheckboxBlock;
use WebEtDesign\CmsBundle\CMS\Block\ChoiceBlock;
use WebEtDesign\CmsBundle\CMS\Block\EntityBlock;
use WebEtDesign\CmsBundle\CMS\Block\StaticBlock;
use WebEtDesign\CmsBundle\CMS\Block\TextareaBlock;
use WebEtDesign\CmsBundle\CMS\Block\TextBlock;
use WebEtDesign\CmsBundle\CMS\Block\WysiwygBlock;
use WebEtDesign\CmsBundle\CMS\Template\AbstractPage;
use WebEtDesign\CmsBundle\CMS\Configuration\BlockDefinition;
use WebEtDesign\CmsBundle\CMS\Configuration\RouteAttributeDefinition;
use WebEtDesign\CmsBundle\CMS\Configuration\RouteDefinition;
use WebEtDesign\MediaBundle\Blocks\MediaBlock;

abstract class WDActualitiesPage extends AbstractPage
{
    public const code = "ACTUALITIES";
    public const routeName = "actualities";

    protected ?string $template = 'pages/actuality/actualities.html.twig';

    protected ?string $label = 'Listing des actualités';

    protected bool $useCategory = true;

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
