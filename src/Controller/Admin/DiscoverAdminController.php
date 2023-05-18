<?php

namespace App\Controller\Admin;

use App\Entity\discover;
use App\Entity\Images;
use App\Repository\DiscoverRepository;
use App\Form\DiscoverForm;
use App\Repository\ImagesRepository;
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

    public function index(): Response
    {
       
        return $this->render('admin/discover/index.html.twig');
    }

    // Route ajout Flat Admin
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
            $discover = $discoverFormulaire->get('image')->getData();

            foreach ($image as $image) {
                // On définit le dossier de destination
                $folder = 'imageBlog';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setPath($fichier);
                $discover->addImage($img);
            }

            $em->persist($discover);
            $em->flush();

            $this->addFlash('success', 'La découverte à été ajouté avec succès');

            // On redirige 
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('discover/add.html.twig', [
            'discoverFormulaire' => $discoverFormulaire->createView()
        ]);
    }

    #[Route('/modidier/{id}', name: 'edit')]
    public function edit(Discover $discover, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // On crée le formulaire
        $flatFormulaire = $this->createForm(FlatForm::class, $flat);

        // On traite la requête du formulaire
        $flatFormulaire->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if ($flatFormulaire->isSubmitted() && $flatFormulaire->isValid()) {

            foreach ($flat->getImages() as $image) {
                // Supprime l'image du dossier
                $pictureService->delete($image->getTitre());
                // Supprime l'image de la collection
                // $flat->removeImage($image);
                $flat->getImages()->removeElement($image);
            }

            $images = $flatFormulaire->get('images')->getData();

            foreach ($images as $image) {
                // On définit le dossier de destination
                $folder = 'flats';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setTitre($fichier);
                $flat->addImage($img);
            }

            // On stocke
            $em->persist($flat);
            $em->flush();

            $this->addFlash(
                'success',
                'Produit modifié avec succès'
            );

            // On redirige
            return $this->redirectToRoute('admin_flat_index');
        }

        return $this->render('admin/flat/edit.html.twig', [
            'flatFormulaire' => $flatFormulaire->createView(),
            'flat' => $flat
        ]);
    }

    #[Route('/supprimer/{id}', name: "delete")]
    public function delete(Flat $flat, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($flat);
        $em->flush();

        $this->addFlash(
            'success',
            'Produit supprimé avec succès'
        );

        return $this->redirectToRoute("admin_flat_index");
    }
}
