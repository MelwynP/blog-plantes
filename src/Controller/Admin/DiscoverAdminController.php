<?php

namespace App\Controller\Admin;

use App\Entity\Discover;
use App\Entity\Image;
use App\Repository\DiscoverRepository;
use App\Repository\ImageRepository;
use App\Form\DiscoverForm;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/admin/discover', name: 'admin_discover_')]

class DiscoverAdminController extends AbstractController
{
    #[Route('/', name: 'index')]

    public function index(DiscoverRepository $discoverRepository, ImageRepository $imageRepository): Response
    {
        $discover = $discoverRepository->findAll();
        $image = $imageRepository->findAll();
        return $this->render('admin/discover/index.html.twig', compact('discover', 'image'));
    }

    #[Route('/ajouter', name: 'add')]

    public function add(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un nouvel objet Photo
        $discover = new Discover();

        //On crée le formulaire
        $discoverFormulaire = $this->createForm(DiscoverForm::class, $discover);

        //On traite la requête du formulaire
        $discoverFormulaire->handleRequest($request);

        if ($discoverFormulaire->isSubmitted() && $discoverFormulaire->isValid()) {
            // On récupère l'image
            $images = $discoverFormulaire->get('image')->getData();

            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'imageBlog';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setPath($fichier);
                $discover->addImage($img);

                
            }

            $em->persist($discover);
            $em->flush();

            $this->addFlash('success', 'La découverte à été ajouté avec succès');

            // On redirige 
            return $this->redirectToRoute('admin_discover_index');
        }

        return $this->render('admin/discover/add.html.twig', [
            'discoverFormulaire' => $discoverFormulaire->createView()
        ]);
    }

    #[Route('/modifier/{id}', name: 'edit')]
    public function edit(Discover $discover, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $existingImages = $discover->getImage()->toArray(); 

        $discoverFormulaire = $this->createForm(DiscoverForm::class, $discover);

        $discoverFormulaire->handleRequest($request);

        if ($discoverFormulaire->isSubmitted() && $discoverFormulaire->isValid()) {

          $images = $discoverFormulaire->get('image')->getData();

          if (!empty($images)) {
            // Supprimer les images existantes seulement si de nouvelles images sont ajoutées
            foreach ($existingImages as $image) {
                $pictureService->delete($image->getPath());
                $discover->removeImage($image);
            }


            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'imageBlog';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setPath($fichier);
                $discover->addImage($img);
            }
        }

            // On stocke
            $em->persist($discover);
            $em->flush();

            $this->addFlash(
                'success',
                'Découverte modifié avec succès'
            );

            // On redirige
            return $this->redirectToRoute('admin_discover_index');
        }

        return $this->render('admin/discover/edit.html.twig', [
            'discoverFormulaire' => $discoverFormulaire->createView(),
            'discover' => $discover,
        ]);
    }

    #[Route('/supprimer/{id}', name: "delete")]
    public function delete(Discover $discover, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($discover);
        $em->flush();

        $this->addFlash(
            'success',
            'Découverte supprimé avec succès'
        );

        return $this->redirectToRoute("admin_discover_index");
    }
}
