<?php

namespace App\Form;

use App\Entity\Discover;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscoverForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('country', TextType::class, [
        'attr' => [
          'placeholder' => 'Le nom du pays',
        ],
        "required" => false,
        'label' => 'Pays',
      ])

      ->add('capital', TextType::class, [
        'attr' => [
          'placeholder' => 'La capitale du pays',
        ],
        "required" => false,
        'label' => 'Capitale',
      ])

      ->add('language', TextType::class, [
        'attr' => [
          'placeholder' => 'La langue du pays',
        ],
        "required" => false,
        'label' => 'Langue',
      ])

      ->add('currency', TextType::class, [
        'attr' => [
          'placeholder' => 'La monnaie du pays',
        ],
        "required" => false,
        'label' => 'Monnaie',
      ])

      ->add('population', TextType::class, [
        'attr' => [
          'placeholder' => 'La population du pays',
        ],
        "required" => false,
        'label' => 'Population',
      ])

      ->add('area', TextType::class, [
        'attr' => [
          'placeholder' => 'La superficie du pays',
        ],
        "required" => false,
        'label' => 'Superficie',
      ])

      ->add('contentIntro', TextType::class, [
        'attr' => [
          'placeholder' => 'Contenu introductif',
        ],
        "required" => false,
        'label' => 'Contenu introductif',
      ])

      ->add('content', TextType::class, [
        'attr' => [
          'placeholder' => 'Contenu',
        ],
        "required" => false,
        'label' => 'Contenu',
      ])

      ->add('contentFooter', TextType::class, [
        'attr' => [
          'placeholder' => 'Contenu finale',
        ],
        "required" => false,
        'label' => 'Contenu finale',
      ])

      ->add('image', FileType::class, [
        'label' => 'Image',
        'multiple' => true,
        'mapped' => false,
        'required' => false,
        'constraints' => [
          new All(
            new Image([
              'maxSize' => '5000k',
              'maxWidth' => 1280,
              'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum',
              'maxSizeMessage' => 'L\'image ne doit pas dépasser 5Mo',
            ])
          )
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Discover::class,
    ]);
  }
}
