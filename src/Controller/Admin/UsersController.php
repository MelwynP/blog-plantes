<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


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


  #[Route('/supprimer/{id}', name: "delete")]
  public function delete(User $user, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $em->remove($user);
    $em->flush();

    $this->addFlash(
      'success',
      'Utilisateur supprimé avec succès, veuillez bloquer ce mail si besoin.'
    );

    return $this->redirectToRoute("admin_user_index");
  }

}
