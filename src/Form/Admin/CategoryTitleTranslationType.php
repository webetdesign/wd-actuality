<?php

namespace WebEtDesign\ActualityBundle\Form\Admin;

use App\Entity\Actuality\Actuality;
use App\Entity\Actuality\ActualityTranslation;
use App\Entity\Actuality\CategoryTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use WebEtDesign\MailerBundle\Entity\MailTranslation;

class CategoryTitleTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                    'label'       => 'Titre',
                    'required'    => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategoryTranslation::class,
        ]);
    }
}
