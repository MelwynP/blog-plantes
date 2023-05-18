<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
  #[Route('/', name: 'index')]

  public function index(UserRepository $userRepository, EntityManagerInterface $em): Response
  {

    $userOrder = $em->getRepository(User::class)
      ->createQueryBuilder('b')
      ->orderBy('b.createdAt', 'ASC')
      ->getQuery()
      ->getResult();

    return $this->render('admin/users/index.html.twig', [
      'user' => $userRepository->findAll(),
      'userOrder' => $userOrder,
    ]);
  }


  #[Route('/supprimer/{id}', name: "delete")]
  #[ParamConverter("user", class:"App\Entity\User")]

  public function delete(User $user, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    if ($user->getEmail() === 'contact@blog-participatif.tech') {
        // Gérer le cas où la suppression de cet utilisateur est interdite
        $this->addFlash(
            'danger',
            'Vous ne pouvez pas supprimer le compte administrateur.'
        );

        return $this->redirectToRoute("admin_users_index");
    }

    $em->remove($user);
    $em->flush();

    $this->addFlash(
      'success',
      'Utilisateur supprimé avec succès, veuillez bloquer ce mail si besoin.'
    );

    return $this->redirectToRoute("admin_users_index");
  }

}
