<?php

namespace WebEtDesign\ActualityBundle\Form\Admin;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\ActualityMedia;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use WebEtDesign\MediaBundle\Form\Type\WDMediaType;

class ActualityMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('actuality', EntityType::class, array_merge([
            'class' => Actuality::class,
        ], $options['actuality']?->getId() ? ['data' => $options['actuality']] : []));

        $builder->add('media', WDMediaType::class, [
            'label' => false,
            'category' => 'actuality_collection',
            'constraints' => [
                  new NotBlank()
            ]
        ]);

        $builder->add('position', HiddenType::class, [
            'data' => $builder->getName(),
            'attr' => [
                'data-actuality-media-collection-target' => 'positionField',
                'data-index'                         => $builder->getName(),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'actuality' => null,
            'data_class' => ActualityMedia::class,
            'translation_domain' => 'actuality__actuality_media_admin'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'actuality_media';
    }
}
