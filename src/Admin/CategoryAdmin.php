<?php

declare(strict_types=1);

namespace WebEtDesign\ActualityBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

class CategoryAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'ASC',
        '_sort_by'    => 'position',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
//        unset($this->listModes['mosaic']);

//            $listMapper
//                ->add('position', 'actions', [
//                    'actions' => [
//                        'move' => [
////                            'template'                  => '@PixSortableBehavior/Default/_sort_drag_drop.html.twig',
//                            'enable_top_bottom_buttons' => false,
//                        ]
//                    ]
//                ]);
        $listMapper
            ->add('title')
            ->add('createdAt', null, ['format' => 'd/m/Y  H:i:s'])
            ->add('updatedAt', null,['format' => 'd/m/Y  H:i:s'])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('position')
            ->add('createdAt',null, ['format' => 'd/m/Y H:i:s'])
            ->add('updatedAt', null, ['format' => 'd/m/Y H:i:s']);
    }

//    protected function configureRoutes(RouteCollectionInterface $collection):void
//    {
//        $collection
//            ->add('move', $this->getRouterIdParameter() . '/move/{position}');
//    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
        parent::configureRoutes($collection);
    }
}
