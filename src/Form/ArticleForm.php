<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('title', TextType::class, [
        'attr' => [
          'class' => 'form-control'
        ],
        'label' => 'Titre',
        'required' => true
      ])

      ->add('content', TextType::class, [
        'attr' => [
          'class' => 'form-control'
        ],
        'label' => 'contenu',
        'required' => false
      ])

      ->add('image', FileType::class, [
        'label' => 'Image',
        'multiple' => true,
        'mapped' => false,
        'required' => false,
        'constraints' => [
          new All(
            new image([
              'maxSize' => '5000k',
              'maxWidth' => 1280,
              'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum',
              'maxSizeMessage' => 'L\'image ne doit pas dépasser 5Mo',
            ])
          )
        ]
      ])

      ->add('category', EntityType::class, [
        'class' => category::class,
        'choice_label' => 'name',
        'label' => 'Catégorie',
        'required' => false,
        'attr' => [
          'class' => 'form-control'
        ]
      ])

      ->add('user', EntityType::class, [
        'class' => user::class,
        'choice_label' => 'pseudo',
        'label' => 'Auteur',
        'required' => false,
        'attr' => [
          'class' => 'form-control'
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Article::class,
      'existingImage' => [],
    ]);
  }
}
