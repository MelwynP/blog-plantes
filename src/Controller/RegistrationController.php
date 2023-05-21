<?php

namespace App\Controller;

use DateTimeImmutable;
use DateTimeZone;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
  #[Route('/inscription', name: 'app_register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    UserAuthenticatorInterface $userAuthenticator,
    UserAuthenticator $authenticator,
    EntityManagerInterface $entityManager,
    SendMailService $sendMailService,
    JWTService $jwtService
  ): Response {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($user->getEmail() === "contact@blog-participatif.tech") {
        $user->setRoles(["ROLE_ADMIN"]) && $user->setIsVerified(true);
      } else {
        $user->setRoles(["ROLE_USER"]);
      }

     
      // encode the plain password
      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $form->get('plainPassword')->getData()
        )
      );

      $user->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));


      $entityManager->persist($user);
      $entityManager->flush();
      // ici faites ce que vous voulez par exemple envoyer un email de verification, mais il faut le service SendMailService, je peux donc ajouter un service pour envoyer un email de verification.

      // On génère le JWT pour l'utilisateur
      // On crée le header
      $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
      ];
      // On crée le payload
      $payload = [
        'user_id' => $user->getId(),
      ];
      // On génère le token
      $token = $jwtService->generate($header, $payload, $this->getParameter('app.jwtsecret'));
      // On peut changer la duré du token ici
      // dd($token); voir le token dans le dd

      //on envoie un email 
      $sendMailService->send(
        'contact@blog-participatif.tech',
        $user->getEmail(),
        //on récupère l'email de l'utilisateur avec la methode getemail()
        'Activation de votre compte ', //le subject
        'register', //le template
        compact('user', 'token')
        // [
        //     'user' => $user,
        //     'token' => $token
        //     ]
        //on passe l'utilisateur dans le tableau. 
        // La syntaxe compact('user') est équivalente
        // Puis on passe le token directement dans le mail
      );

      return $userAuthenticator->authenticateUser(
        $user,
        $authenticator,
        $request
      );
    }

    return $this->render('registration/register.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/verification/{token}', name: 'verify_user')]
  // On passe a notre fonction le token que l'on récupère dans l'url grace a la route 
  public function verifyUser($token, JWTService $jwtService, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
  {
    // On vérifie si le token est valide, qu'il n'a pas expiré, qu'il n'a pas été modifié
    if ($jwtService->isValid($token) && !$jwtService->isExpired($token) && $jwtService->check($token, $this->getParameter('app.jwtsecret'))) {
      // Si toutes ces conditions sont réunis ca veut dire que le token est valide et je peux activer l'utilisateur
      // On récupère le payload du token
      $payload = $jwtService->getPayload($token);

      // On récupère l'utilisateur du token on ajoute le Repository a la function
      // Dans le payload on a l'id de l'utilisateur, on va donc chercher l'utilisateur en bdd grace a son id
      $user = $userRepository->find($payload['user_id']);

      // On vérifie si l'utilisateur existe et n'est pas déjà activé, on ajoute EntityManagerInterface a la function
      if ($user && !$user->getIsVerified()) {
        $user->setIsVerified(true);
        $entityManager->flush($user);
        $this->addFlash('success', 'Votre compte est activé, vous pouvez maintenant modifier vos informations personnelles');
        return $this->redirectToRoute('app_post_index');
      }
    }
    // Ici un problème se pose dans le token
    $this->addFlash('danger', 'Le lien d\'identification est invalide où à expiré');
    return $this->redirectToRoute('app_login');
  }

  #[Route('/renvoi-verification', name: 'resend_verification')]
  // On renvoie un email de verification a l'utilisateur connecté 
  public function resendVerif(JWTService $jwtService, SendMailService $sendMailService, UserRepository $userRepository): Response
  {
    // On récupère l'utilisateur connecté
    $user = $this->getUser();

    // On vérifie si l'utilisateur est connecté et qu'il n'est pas déjà activé
    if (!$user) {
      $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
      return $this->redirectToRoute('app_login');
    }

    if ($user->getIsVerified()) {
      $this->addFlash('warning', 'Votre compte est déjà activé');
      return $this->redirectToRoute('app_account');
    }
    // On génère le JWT pour l'utilisateur
    // On crée le header
    $header = [
      'alg' => 'HS256',
      'typ' => 'JWT'
    ];
    // On crée le payload
    $payload = [
      'user_id' => $user->getId(),
    ];
    // On génère le token
    $token = $jwtService->generate($header, $payload, $this->getParameter('app.jwtsecret'));
    // On peut changer la duré du token ici
    // dd($token); voir le token dans le dd

    //on envoie un email 
    $sendMailService->send(
      'contact@blog-participatif.tech',
      $user->getEmail(),
      //on récupère l'email de l'utilisateur avec la methode getemail()
      'Activation de votre compte Quai Antique', //le subject
      'register', //le template
      [
        'user' => $user,
        'token' => $token
        //on passe l'utilisateur dans le tableau. 
        // La syntaxe compact('user') est équivalente
        // Puis on passe le token directement dans le mail
      ]
    );
    $this->addFlash('success', 'Email de vérification envoyé');
    return $this->redirectToRoute('app_post_index');
  }
}
