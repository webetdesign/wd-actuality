<?php

namespace WebEtDesign\ActualityBundle\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use WebEtDesign\ActualityBundle\Entity\Actuality;
use WebEtDesign\ActualityBundle\Entity\Category;
use WebEtDesign\CmsBundle\Controller\BaseCmsController;

class ActualityController extends BaseCmsController
{

    /**
     * @param Request $request
     * @param Actuality $actuality
     * @return Response|ResourceNotFoundException
     * @ParamConverter("actuality", class="WebEtDesign\ActualityBundle\Entity\Actuality", options={"mapping": {"actuality": "slug"}})
     */
    public function __invoke(Request $request, Actuality $actuality){

        if (!$actuality) {
            return new ResourceNotFoundException();
        }

        return $this->defaultRender([
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
        $actualityRepo = $this->getDoctrine()->getRepository(Actuality::class);
        if ($category) {
            $qb = $actualityRepo->findPublishedByCategory($category);
        } else {
            $qb = $actualityRepo->findPublished();
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setMaxPerPage((int) $request->query->get('limit', 9));

        return $this->defaultRender([
            'categories' => $categories,
            'category'   => $category,
            'pager'      => $pager,
        ]);
    }
}
