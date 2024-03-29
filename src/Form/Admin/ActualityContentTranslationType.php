<?php

namespace WebEtDesign\ActualityBundle\Form\Admin;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\ActualityTranslation;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use WebEtDesign\MailerBundle\Entity\MailTranslation;

class ActualityContentTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('excerpt', CKEditorType::class,
                [
                    'label' => 'Extrait',
                    'required'         => false,
                    'attr'             => [
                        'rows' => 5
                    ],
                    'help' => 'A short introducing text'
                ])
            ->add('content', CKEditorType::class,
                [
                    'label' => 'Contenu',
                    'required'         => false,
                    'attr'             => [
                        'rows' => 15
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ActualityTranslation::class,
        ]);
    }
}
