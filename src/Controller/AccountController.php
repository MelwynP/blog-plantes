<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


#[Route('/mon-compte', name: 'app_account_')]
class AccountController extends AbstractController
{
  #[Route('/', name: 'index')]
  public function index(): Response
  {

    return $this->render('account/index.html.twig');
  }

  #[Route('/modifier/{email}', name: "edit_email")]
  public function update(Request $request, string $email, User $user, EntityManagerInterface $em): Response
  {
    $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
    if (!$user) {
      throw $this->createNotFoundException('L\'utilisateur n\'existe pas.');
    }

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


    if ($this->getUser()->getEmail() !== $email) {
      $this->addFlash("error", "Vous ne pouvez pas modifier.");
      return $this->redirectToRoute("app_account_index");
    }

    $updateUserForm = $this->createForm(UpdateUserFormType::class, $user);
    $updateUserForm->handleRequest($request);

    if ($updateUserForm->isSubmitted() && $updateUserForm->isValid()) {

      $em->flush();

      $this->addFlash('success', 'Vos données ont été mises à jour.');

      return $this->redirectToRoute("app_account_index");
    }
    return $this->render('account/edit.html.twig', [
      "updateUserForm" => $updateUserForm->createView()
    ]);
  }



#[Route('/supprimer/{email}', name: 'delete')]
public function delete(User $user, EntityManagerInterface $em): Response
  {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($user->getEmail() === 'contact@blog-participatif.tech') {
        $this->addFlash('danger', 'Vous ne pouvez pas supprimer le compte administrateur.');
        return $this->redirectToRoute('admin_users_index');
        }

        //suppression de la session a revoir
        $session = new Session();
        $session->invalidate();
        
        $em->remove($user);
        $em->flush();


        return $this->redirectToRoute('app_post_index');
      }
  




//  #[Route('/supprimer/{email}', name: "delete")]
//   #[ParamConverter("user", class:"App\Entity\User")]  

//   public function delete(string $email, User $user, EntityManagerInterface $em): Response
//   {

//     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

//     if ($user->getEmail() === 'contact@blog-participatif.tech') {
//         // Gérer le cas où la suppression de cet utilisateur est interdite
//         $this->addFlash(
//             'danger',
//             'Vous ne pouvez pas supprimer le compte administrateur.'
//         );
//                 return $this->redirectToRoute("admin_users_index");
//     }

//     $em->remove($user);
//     $em->flush();

//     $this->addFlash(
//       'success',
//       'Compte supprimé avec succès'
//     );
//     return $this->redirectToRoute("app_post_index");
//   }
}
