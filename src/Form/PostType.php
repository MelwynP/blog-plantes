<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add("title", TextType::class, [
        "label" => "Titre",
        "required" => false,
        "constraints" => [new Length(["min" => 0, "max" => 150, "minMessage" => "Le titre ne doit pas faire plus de 150 caractères", "maxMessage" => "Le titre ne doit pas faire plus de 150 caractères"]),]
      ])

      ->add("content", TextareaType::class, [
        "label" => "Contenu",
        "required" => true,
        "constraints" => [
          new Length(["min" => 5, "max" => 320, "minMessage" => "Le contenu doit faire entre 5 et 320 caractères", "maxMessage" => "Le contenu doit faire entre 5 et 320 caractères"]),
          new NotBlank(["message" => 'Le contenu ne doit pas être vide !'])
        ]
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

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      "data_class" => Post::class,
      ]);
  }
}
