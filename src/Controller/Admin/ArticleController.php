<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Image;
use App\Repository\ArticleRepository;
use App\Form\ArticleForm;
use App\Repository\ImageRepository;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/article', name: 'admin_article_')]
class ArticleController extends AbstractController
{
  #[Route('/', name: 'index')]

  public function index(ArticleRepository $articleRepository, ImageRepository $imageRepository): Response
  {
    $article = $articleRepository->findAll();
    $image = $imageRepository->findAll();
    return $this->render('admin/article/index.html.twig', compact('article', 'image'));
  }

  // Route ajout article
  #[Route('/ajouter', name: 'add')]

  public function add(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $article = new Article();
    $articleFormulaire = $this->createForm(ArticleForm::class, $article);
    $articleFormulaire->handleRequest($request);

    if ($articleFormulaire->isSubmitted() && $articleFormulaire->isValid()) {
      // On récupère l'image
      $images = $articleFormulaire->get('image')->getData();

      foreach ($images as $image) {
        // On définit le dossier de destination
        $folder = 'imageBlog';

        // On appelle le service d'ajout
        $fichier = $pictureService->add($image, $folder, 300, 300);

        $img = new Image();
        $img->setPath($fichier);
        $img->setArticle($article);
        $article->addImage($img);

        // $article = $articleFormulaire->get('article')->getData();
      }

      // $article->setUser($this->getUser());

      $em->persist($article);
      $em->flush();


      $this->addFlash('success', 'Article ajouté avec succès');
      
      // On redirige vers la liste
      return $this->redirectToRoute('admin_article_index');
    }

    return $this->render('admin/article/add.html.twig', [
      'articleFormulaire' => $articleFormulaire->createView()
    ]);
  }

  #[Route('/modidier/{id}', name: 'edit')]
  public function edit(Article $article, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    // Copie des images existantes
    $existingImages = $article->getImage()->toArray(); 

    $articleFormulaire = $this->createForm(ArticleForm::class, $article);
    $articleFormulaire->handleRequest($request);

    if ($articleFormulaire->isSubmitted() && $articleFormulaire->isValid()) {

      $images = $articleFormulaire->get('image')->getData();

      if (!empty($images)) {
        // Supprimer les images existantes seulement si de nouvelles images sont ajoutées
        foreach ($existingImages as $image) {
            $pictureService->delete($image->getPath());
            $article->removeImage($image);
        }

      foreach ($images as $image) {
        // On définit le dossier de destination
        $folder = 'imageBlog';

        // On appelle le service d'ajout
        $fichier = $pictureService->add($image, $folder, 300, 300);

        $img = new Image();
        $img->setPath($fichier);
        $article->addImage($img);
      }
    }

      // On stocke
      $em->persist($article);
      $em->flush();

      $this->addFlash(
        'success', 'article modifié avec succès'
      );

      // On redirige
      return $this->redirectToRoute('admin_article_index');
    }

    return $this->render('admin/article/edit.html.twig', [
      'articleFormulaire' => $articleFormulaire->createView(),
      'article' => $article
    ]);
  }


  #[Route('/supprimer/{id}', name: "delete")]
  public function delete(Article $article, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $em->remove($article);
    $em->flush();

    $this->addFlash(
      'success',
      'article supprimé avec succès'
    );

    return $this->redirectToRoute("admin_article_index");
  }
}
