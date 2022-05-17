<?php

namespace WebEtDesign\ActualityBundle\Controller;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
//use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sonata\Doctrine\Adapter\ORM\DoctrineORMAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use WebEtDesign\ActualityBundle\Cms\ActualityVars;
use WebEtDesign\ActualityBundle\Entity\WDActuality;
use WebEtDesign\CmsBundle\Controller\BaseCmsController;

class ActualityController extends BaseCmsController
{
    protected $config;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * @inheritDoc
    */
    public function __construct($config, ParameterBagInterface $parameterBag) {
        $this->config = $config;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param WDActuality $actuality
     * @return Response|ResourceNotFoundException
     * @ParamConverter("actuality", class="App\Entity\Actuality\Actuality", options={"mapping": {"actuality": "slug"}})
     * @ParamConverter("category", class="App\Entity\Acyuality\Category", options={"mapping": {"category": "slug"}})
     */
    public function __invoke(Request $request, Category $category, WDActuality $actuality){

        if (!$category || !$actuality) {
            return new ResourceNotFoundException();
        }
        $def = $this->parameterBag->get('wd_cms.vars');

        if  (isset($def['enable']) && $def['enable']){
            $this->setVarsObject(new ActualityVars($actuality));
        }

        return $this->defaultRender([
            'category' => $category,
            'actuality' => $actuality
        ]);
    }

    /**
     * @param Request $request
     * @param Category|null $category
     * @return Response
     * @ParamConverter("category", class="WebEtDesign\ActualityBundle\Entity\Category", options={"mapping": {"category": "slug"}})
     */
    public function list(Request $request, Category $category = null)
    {
        $actualityRepo = $this->getDoctrine()->getRepository(WDActuality::class);
        if ($category) {
            $qb = $actualityRepo->findPublishedByCategory($category);
        } else {
            $qb = $actualityRepo->findPublished();
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setMaxPerPage((int) $request->query->get('limit', $this->config['result_limit']));

        return $this->defaultRender([
            'categories' => $categories,
            'category'   => $category,
            'pager'      => $pager,
        ]);
    }
}
