<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Flat;
use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlatForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', TextType::class, [
        'attr' => [
          'class' => 'form-control'
        ],
        'label' => 'Nom'
      ])

      ->add('titre', TextType::class, [
        'attr' => [
          'class' => 'form-control'
        ],
        'label' => 'Titre',
        'required' => false
      ])

      ->add('description', TextType::class, [
        'attr' => [
          'class' => 'form-control'
        ],
        'label' => 'Description',
        'required' => false

      ])

      ->add('price', MoneyType::class, [
        'label' => 'Prix',
        // Si prix en centime 'divisor' => 100,
        'constraints' => [
          new Positive(
            message: 'Le prix ne peut être négatif'
          )
        ]
      ])

      ->add('images', FileType::class, [
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
      ])

      ->add('category', EntityType::class, [
        'class' => Category::class,
        'choice_label' => 'name',
        'label' => 'Catégorie',
        'required' => false,
        'attr' => [
          'class' => 'form-control'
        ]
      ])

      ->add('menu', EntityType::class, [
        'class' => Menu::class,
        'choice_label' => 'name',
        'label' => 'Menu',
        'required' => false,
        'attr' => [
          'class' => 'form-control'
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Flat::class,
    ]);
  }
}
