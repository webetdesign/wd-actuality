<?php

namespace WebEtDesign\ActualityBundle\Controller;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\Category;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use WebEtDesign\ActualityBundle\Cms\ActualityVars;
use WebEtDesign\CmsBundle\Controller\BaseCmsController;

#[AsController]
class ActualityController extends BaseCmsController
{
    protected $config;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    private bool $useCategory;
    private EntityManagerInterface $em;

    /**
     * @inheritDoc
    */
    public function __construct(
        $config,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $em
    ) {
        $this->config = $config;
        $this->parameterBag = $parameterBag;
        $this->useCategory = $parameterBag->get('wd_actuality.config')['use_category'];
        $this->em = $em;
    }

    /**
     * @param Request $req
     * @return Response|ResourceNotFoundException
     */
    public function __invoke(Request $request, $actuality, $category = null){
        $locale = $request->getLocale();
        $actuRepo = $this->em->getRepository(Actuality::class);
        $catRepo = $this->em->getRepository(Category::class);
        $category = $catRepo->findOneBySlug($category, $locale);
        $actuality = $actuRepo->findOneBySlug($actuality, $locale);


        if (!$actuality || ($this->useCategory && !$category)) {
            throw new NotFoundHttpException();
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
    public function list(Request $request, $category = null)
    {
        $locale = $request->getLocale();
        $actuRepo = $this->em->getRepository(Actuality::class);
        $catRepo = $this->em->getRepository(Category::class);

        $category = $catRepo->findOneBySlug($category, $locale);

        if ($category) {
            $qb = $actuRepo->findPublishedByCategory($category);
        } else {
            $qb = $actuRepo->findPublished();
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $pager = new Pagerfanta(new QueryAdapter($qb));
        $pager->setMaxPerPage((int) $request->query->get('limit', $this->config['result_limit']));
        $pager->setCurrentPage($request->query->get('page', 1));

        return $this->defaultRender([
            'categories' => $categories,
            'category'   => $category,
            'pager'      => $pager,
        ]);
    }
}
