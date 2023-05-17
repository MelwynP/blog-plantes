<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
  #[Route('/', name: 'index')]

  public function index(UserRepository $userRepository): Response
  {
    return $this->render('admin/users/index.html.twig', [
      'user' => $userRepository->findAll(),
    ]);
  }

  // Mettre en place la route pour supprimer un utilisateur
}
