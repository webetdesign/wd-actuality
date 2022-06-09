<?php

declare(strict_types=1);

namespace WebEtDesign\ActualityBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use WebEtDesign\ActualityBundle\Form\Admin\ActualityTitleTranslationType;
use WebEtDesign\ActualityBundle\Form\Admin\CategoryTitleTranslationType;

class CategoryAdmin extends AbstractAdmin
{
    protected array $locales;
    protected string $defaultLocale;
    protected ParameterBagInterface $parameterBag;

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'ASC',
        '_sort_by'    => 'position',
    ];

    public function __construct(
        ?string $code = null,
        ?string $class = null,
        ?string $baseControllerName = null,
        ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->parameterBag = $parameterBag;
        $this->locales = $parameterBag->get('wd_actuality.translation.locales');
        $this->defaultLocale = $parameterBag->get('wd_actuality.translation.default_locale');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title', CallbackFilter::class, [
                'callback' => function (ProxyQueryInterface $query, $alias, $field, $data) {
                    if (!$data instanceof FilterData) {
                        return false;
                    }

                    $query
                        ->join($alias . '.translations', 't')
                        ->andWhere('LOWER(t.title) LIKE :title')
                        ->andWhere('t.locale = :locale')
                        ->setParameters([
                            'title' => '%' . strtolower($data->getValue()) . '%',
                            'locale' => $this->defaultLocale
                        ]);

                    return true;
                }
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
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
            ->add('translationsTitle', TranslationsFormsType::class, [
                'label'            => false,
                'locales'          => $this->locales,
                'default_locale'   => $this->defaultLocale,
                'required_locales' => [$this->defaultLocale],
                'form_type'        => CategoryTitleTranslationType::class,
            ]);
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

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
        parent::configureRoutes($collection);
    }
}
