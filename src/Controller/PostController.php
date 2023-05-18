<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Image;
use App\Form\PostType;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Repository\ImageRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/', name: 'app_post_')]
class PostController extends AbstractController
{
  #[Route('/', name: 'index')]

    // function qui recupere tout le contenu de la table post avec la methode findAll() de la class PostRepository et qui l'envoie dans la vue index.html.twig 
    public function index(Request $request, PostRepository $postRepository, ArticleRepository $articleRepository, ImageRepository $imageRepository, CategoryRepository $categoryRepository, UserRepository $userRepository): Response
    {
        $search = $request->request->get("search"); // $_POST["search"]
        $posts = $postRepository->findAll(); // SELECT * FROM `post`;
        if ($search) {
          $posts = $postRepository->findBySearch($search); // SELECT * FROM `post` WHERE title LIKE :search;
        }

      return $this->render('post/index.html.twig', [
      'post' => $postRepository->findAll(),
      'user' => $userRepository->findAll(),
      'article' => $articleRepository->findAll(),
      'image' => $imageRepository->findAll(),
      'category' => $categoryRepository->findAll(),
      'search' => $search,
      ]);
  }
      

    #[Route('/ajouter', name: 'add')]
    // function qui permet de creer un nouveau post si l'utilisateur est connecté 
    public function create(Request $request,EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $PostForm = $this->createForm(PostType::class, $post);
        $PostForm->handleRequest($request);
        if ($PostForm->isSubmitted() && $PostForm->isValid()) {
            $image = $PostForm->get('image')->getData();
              //  si l'utilisateur a ajouté une image, on la traite et on l'enregistre dans le dossier public/uploads 
              foreach ($image as $image) {
                // On définit le dossier de destination
                $folder = 'imageBlog';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Image();
                $img->setPath($fichier);
                $post->addImage($img);
              }
              
              // association du post a l'utilisateur connecté
            $post->setUser($this->getUser());
            $post->setPublishedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            // $post->setPublishedAt(new \DateTime());
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post ajouté avec succès');

            return $this->redirectToRoute('app_post_index');
            }

        return $this->render('post/add.html.twig', [
            "PostForm" => $PostForm->createView(),
        ]);
    }

    #[Route('/modifier/{id}', name: 'edit')]
    public function update(Post $post, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $post->getUser()) {
          $this->addFlash("error", "Vous ne pouvez pas modifier une publication qui ne vous appartient pas.");
          return $this->redirectToRoute('app_post_index');
        }

        $PostForm = $this->createForm(PostType::class, $post);
        $PostForm->handleRequest($request);
        if ($PostForm->isSubmitted() && $PostForm->isValid()) {


      foreach ($post->getImage() as $image) {
        // Supprime l'image du dossier
        $pictureService->delete($image->getPath());
        // Supprime l'image de la collection
        $post->getImage()->removeElement($image);
      }

      $images = $PostForm->get('images')->getData();

      foreach ($images as $image) {
        // On définit le dossier de destination
        $folder = 'imageBlog';

        // On appelle le service d'ajout
        $fichier = $pictureService->add($image, $folder, 300, 300);

        $img = new Image();
        $img->setPath($fichier);
        $post->addImage($img);
      }

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post modifié avec succès');

            return $this->redirectToRoute("app_post_index");
        }
        return $this->render('post/edit.html.twig', [
            "PostForm" => $PostForm->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete')]
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $post->getUser()) {
          $this->addFlash("error", "Vous ne pouvez pas supprimer une publication qui ne vous appartient pas.");
          return $this->redirectToRoute("app_post_index");
        }
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post supprimé avec succès');
        return $this->redirectToRoute("app_post_index");
    }

}