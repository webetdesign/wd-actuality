<?php

namespace WebEtDesign\ActualityBundle\Controller;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
use App\Repository\Actuality\ActualityRepository;
use App\Repository\Actuality\CategoryRepository;
use DateTime;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use WebEtDesign\ActualityBundle\Cms\ActualityVars;
use WebEtDesign\CmsBundle\Controller\BaseCmsController;

class ActualityController extends BaseCmsController
{
    protected $config;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    private bool $useCategory;
    private CategoryRepository $categoryRepo;
    private ActualityRepository $actualityRepo;

    /**
     * @inheritDoc
    */
    public function __construct(
        $config,
        ParameterBagInterface $parameterBag,
        CategoryRepository $categoryRepo,
        ActualityRepository $actualityRepo,
    ) {
        $this->config = $config;
        $this->parameterBag = $parameterBag;
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
        $this->categoryRepo = $categoryRepo;
        $this->actualityRepo = $actualityRepo;
    }

    /**
     * @param Request $req
     * @return Response|ResourceNotFoundException
     */
    public function __invoke(Request $request, $actuality, $category = null){

        $locale = $request->getLocale();
        $category = $this->categoryRepo->findOneBySlug($category, $locale);
        $actuality = $this->actualityRepo->findOneBySlug($actuality, $locale);

        if (!$actuality || ($this->useCategory && !$category)) {
            return new ResourceNotFoundException();
        }


        $now = new DateTime('now');

        if (!$actuality->getPublished() || $actuality->getPublishedAt() === null || $actuality->getPublishedAt()->getTimestamp() > $now->getTimestamp()) {
            throw new AccessDeniedHttpException();
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
     * @return Response
     */
    public function list(Request $request, $category)
    {
        $locale = $request->getLocale();
        $category = $this->categoryRepo->findOneBySlug($category, $locale);

        if ($category) {
            $qb = $this->actualityRepo->findPublishedByCategory($category);
        } else {
            $qb = $this->actualityRepo->findPublished();
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $pager = new Pagerfanta(new QueryAdapter($qb));
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setMaxPerPage((int) $request->query->get('limit', $this->config['result_limit']));

        return $this->defaultRender([
            'categories' => $categories,
            'category'   => $category,
            'pager'      => $pager,
        ]);
    }
}
