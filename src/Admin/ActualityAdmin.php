<?php

declare(strict_types=1);

namespace WebEtDesign\ActualityBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Validator\Constraints\NotNull;
use WebEtDesign\MediaBundle\Form\Type\WDMediaType;
use WebEtDesign\SeoBundle\Admin\SmoOpenGraphAdminTrait;
use WebEtDesign\SeoBundle\Admin\SmoTwitterAdminTrait;

final class ActualityAdmin extends AbstractAdmin
{

    use SmoOpenGraphAdminTrait;
    use SmoTwitterAdminTrait;

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'publishedAt',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('published');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->addIdentifier('title')
            ->addIdentifier('category')
            ->add('published')
            ->add('publishedAt')
            ->add('createAt')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Actuality');

        $formMapper
            ->with('General', ['class' => 'col-md-8', 'box_class' => ''])
            ->add('title')
            ->add('picture', WDMediaType::class, [
                'category' => 'actuality'
            ])
            ->add('category', ModelListType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull()
                ]
            ])
            ->end();

        $formMapper
            ->with('Publication', ['class' => 'col-md-4', 'box_class' => ''])
            ->add('published')
            ->add('publishedAt', DatePickerType::class)
            ->end();

        $formMapper
            ->with('Content', ['box_class' => ''])
            ->add('excerpt', SimpleFormatterType::class,
                [
                    'required'         => false,
                    'format'           => 'richhtml',
                    'ckeditor_context' => 'actuality',
                    'attr'             => [
                        'rows' => 5
                    ]
                ])
            ->addHelp('excerpt', 'A short introducing text')
            ->add('content', SimpleFormatterType::class,
                [
                    'required'         => false,
                    'format'           => 'richhtml',
                    'ckeditor_context' => 'actuality',
                    'attr'             => [
                        'rows' => 15
                    ]
                ])
<<<<<<< Updated upstream
=======
            ->add('pictures', ActualityMediaCollectionType::class, [
                'entry_type' => ActualityMediaType::class,
                'entry_options' => [
                    'actuality' => $this->getSubject()
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'  => false,
                'block_prefix' => 'test'
            ])
>>>>>>> Stashed changes
            ->end();

        $formMapper
            ->end();
        $formMapper
            ->tab('SEO');
        $formMapper->with('Général', ['class' => 'col-xs-12 col-md-4', 'box_class' => ''])
            ->add('seoTitle')
            ->add('seoDescription')
            ->end();
        $this->addFormFieldSmoOpenGraph($formMapper);
        $this->addFormFieldSmoTwitter($formMapper);
        $formMapper
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('picture')
            ->add('excerpt')
            ->add('content')
            ->add('createAt')
            ->add('updatedAt')
            ->add('published')
            ->add('publishedAt');
    }
}
