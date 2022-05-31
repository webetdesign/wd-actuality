<?php

namespace WebEtDesign\ActualityBundle\Controller;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
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

    /**
     * @inheritDoc
    */
    public function __construct($config, ParameterBagInterface $parameterBag) {
        $this->config = $config;
        $this->parameterBag = $parameterBag;
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
    }

    /**
     * @param Request $req$now = new DateTime('now');
     * @param Category $category
     * @param Actuality $actuality
     * @return Response|ResourceNotFoundException
     * @ParamConverter("actuality", class="App\Entity\Actuality\Actuality", options={"mapping": {"actuality": "slug"}})
     * @ParamConverter("category", class="App\Entity\Actuality\Category", options={"mapping": {"category": "slug"}})
     */
    public function __invoke(Request $request, Actuality $actuality, Category $category = null){

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
     * @param Category|null $category
     * @return Response
     * @ParamConverter("category", class="App\Entity\Actuality\Category", options={"mapping": {"category": "slug"}})
     */
    public function list(Request $request, Category $category = null)
    {
        $actualityRepo = $this->getDoctrine()->getRepository(Actuality::class);

        if ($category) {
            $qb = $actualityRepo->findPublishedByCategory($category);
        } else {
            $qb = $actualityRepo->findPublished();
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
