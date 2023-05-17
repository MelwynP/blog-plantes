<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
