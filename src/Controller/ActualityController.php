<?php

namespace WebEtDesign\ActualityBundle\Controller;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
use DateTime;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

    private ManagerRegistry $doctrine;

    /**
     * @inheritDoc
    */
    public function __construct($config, ParameterBagInterface $parameterBag, ManagerRegistry $doctrine) {
        $this->config = $config;
        $this->doctrine = $doctrine;
        $this->parameterBag = $parameterBag;
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
    }

    /**
     * @param Request $req$now = new DateTime('now');
     * @param Category $category
     * @param Actuality $actuality
     * @return Response|ResourceNotFoundException
     */
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['actuality' => 'slug'])] Actuality $actuality,
        #[MapEntity(mapping: ['category' => 'slug'])] ?Category $category = null
    ){

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
     */
    public function list(Request $request, #[MapEntity(mapping: ['category' => 'slug'])] ?Category $category = null)
    {
        $actualityRepo = $this->doctrine->getRepository(Actuality::class);

        if ($category) {
            $qb = $actualityRepo->findPublishedByCategory($category);
        } else {
            $qb = $actualityRepo->findPublished();
        }

        $categories = $this->doctrine->getRepository(Category::class)->findAll();

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
