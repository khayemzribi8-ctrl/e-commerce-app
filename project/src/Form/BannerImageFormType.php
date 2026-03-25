<?php

namespace App\Form;

use App\Entity\BannerImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BannerImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => false,
                'attr' => ['placeholder' => 'Titre de la bannière'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description de la bannière',
                    'rows' => 3,
                ],
            ])
            ->add('buttonText', TextType::class, [
                'label' => 'Texte du bouton',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Découvrir nos produits'],
            ])
            ->add('buttonUrl', TextType::class, [
                'label' => 'URL du bouton',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: /shop'],
            ])
            ->add('imagePath', FileType::class, [
                'label' => 'Image de la bannière',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez charger une image valide (JPG, PNG, GIF, WebP)',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BannerImage::class,
        ]);
    }
}
