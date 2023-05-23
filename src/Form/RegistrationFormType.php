<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('pseudo', TextType::class, [
        'attr' => [
          'placeholder' => 'Ecrivez votre pseudo ici',
        ],
        'label' => 'Pseudo *',
        'required' => true
      ])

      ->add('name', TextType::class, [
        'attr' => [
          'placeholder' => 'Ecrivez votre nom ici',
        ],
        'label' => 'Nom',
        'required' => false,
      ])

      ->add('email', EmailType::class, [
        'attr' => [
          'placeholder' => 'exemple@domaine.com',
        ],
        'required' => true,
        'label' => 'E-mail *'
      ])

      ->add('plainPassword', PasswordType::class, [
        'mapped' => false,
        'attr' => [
          'autocomplete' => 'new-password',
          'placeholder' => '********',
        ],
        'constraints' => [
          new Regex([
            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/',
            'message' => 'Votre mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'
          ]),
        ],
        'label' => 'Mot de passe *'
      ])

      ->add('RGPDConsent', CheckboxType::class, [
        'mapped' => false,
        'constraints' => [
          new IsTrue([
            'message' => 'Vous devez accepter les conditions d\'utilisation.',
          ]),
        ],
        'label' => 'J\'accepte que mes données personnelles soient utilisées pour le traitement de ma demande.',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
