<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  #[Route(path: '/connexion', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // if ($this->getUser()) {
    //     return $this->redirectToRoute('target_path');
    // }
    //Route de redirection si l'utilisateur est connecté on l'envoi sur ... (à définir)
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error
    ]);
  }

  #[Route(path: '/deconnexion', name: 'app_logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  #[Route(path: '/mot-de-passe-oublie', name: 'app_forgot_password')]
  public function forgotPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, SendMailService $sendMailService): Response
  {
    // On lui montre le chemin de notre form
    $form = $this->createForm(ResetPasswordRequestType::class);

    // On récupére notre form avec handleRequest puis on l'appel comme on veut 
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      // On va chercher l'utilisateur par son email avec findOneByEmail et on va chercher l'email qui
      // A été tappé dans le formulaire
      $user = $userRepository->findOneByEmail($form->get('email')->getData());

      // On va chercher les données, du champ email du formulaire
      // On vérifie si l'on a un utilisateur avec condition
      if ($user) {
        // On génère un token de réinitialisation : 1ere etape
        $token = $tokenGenerator->generateToken();
        $user->setResetToken($token);
        $entityManager->persist($user);
        // On peut mettre ici des try / catch
        $entityManager->flush();

        // On génère un lien de réinitialisation du mot de passe :
        $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // En ayant fais cela ca va permettre d'avoir un lien directement dans mon mail
        // On crée les données du mail, c'est un tableau
        $context = compact('url', 'user');

        // $context = [
        //     'user' => $user,
        //     'url' => $url
        // ];

        // On envoie le mail
        $sendMailService->send(
          'contact@quai-antique.tech',
          //recupere le mail de l'utilisateur
          $user->getEmail(),
          'réinitialisation de mot de passe',
          'password_reset',
          $context
          // on peut faire try / catch
        );

        $this->addFlash('success', 'Email de réinitialisation du mot de passe envoyé avec succès');
        return $this->redirectToRoute('app_login');
      }

      // Si l'utilisateur est null 
      $this->addFlash('danger', 'Un problème est survenu');
      return $this->redirectToRoute('app_login');
    }

    // On a la demande de reset + le renvoi de pass sur ce template
    return $this->render('security/reset_password_request.html.twig', [
      // On passe au render le formulaire, on fais un tableau avec le nom que l'on veut du form,
      // En fessant cela tu passes la vue au formulaire et tu prends le nom de requestPassForm 
      'requestPassForm' => $form->createView()
    ]);
  }


  // 2eme etape, donc une nouvelle route
  #[Route(path: '/oubli-mot-de-passe/{token}', name: 'reset_pass')]
  public function resetPass(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $PasswordHasher): Response
  {
    // On vérifie si on a ce token dans la base
    $user = $userRepository->findOneByResetToken($token);
    // dd($user);
    // On refais une condition 
    if ($user) {
      // Si on a un user, on va devoir crée un formulaire
      $form = $this->createForm(ResetPasswordType::class);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        // On efface le token
        $user->setResetToken('');
        $user->setPassword(
          $PasswordHasher->hashPassword(
            $user,
            $form->get('password')->getData()
          )
        );
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Mot de passe changé avec succès');
        return $this->redirectToRoute('app_login');
        // Il faut ajouter de la sécurité

      }

      return $this->render('security/reset_password.html.twig', [
        'passForm' => $form->createView()
      ]);
    }
    $this->addFlash('danger', 'jeton invalide');
    return $this->redirectToRoute('app_login');
  }
}
