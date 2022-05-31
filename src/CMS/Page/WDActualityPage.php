<?php

namespace WebEtDesign\ActualityBundle\CMS\Page;

use App\Entity\Actuality\Actuality;
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


abstract class WDActualityPage extends AbstractPage
{
    public const code = "ACTUALITY";
    public const routeName = "actuality";

    protected ?string $template = 'pages/actuality/actuality.html.twig';

    protected ?string $label = 'Page actualité';

    private bool $useCategory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
    }
    
    public function getRoute(): ?RouteDefinition
    {
        $routeDefinition = RouteDefinition::new()
            ->setController(ActualityController::class)
            ->setName( self::routeName);
        
        if ($this->useCategory) {
            $routeDefinition
                ->setAttributes([
                    RouteAttributeDefinition::new('category')
                        ->setEntityClass(Category::class)
                        ->setEntityProperty('slug')
                        ->setDefault(null)
                    ,
                    RouteAttributeDefinition::new('actuality')
                        ->setEntityClass(Actuality::class)
                        ->setEntityProperty('slug'),
                ])
                ->setPath('/actualite/{category}/{actuality}');
        }else{
            $routeDefinition
                ->setAttributes([
                    RouteAttributeDefinition::new('actuality')
                        ->setEntityClass(Actuality::class)
                        ->setEntityProperty('slug'),
                ])
                ->setPath('/actualite/{actuality}');
        }
        
        return $routeDefinition;
    }
}
