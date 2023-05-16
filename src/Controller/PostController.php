<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Image;
use App\Repository\PostRepository;
use App\Form\PostType;
use App\Repository\ImageRepository;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/', name: "home")]
class PostController extends AbstractController
{
    // function qui recupere tout le contenu de la table post avec la methode findAll() de la class PostRepository et qui l'envoie dans la vue home.html.twig 
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $search = $request->request->get("search"); // $_POST["search"]
        $posts = $postRepository->findAll(); // SELECT * FROM `post`;
        if ($search) {
            $posts = $postRepository->findBySearch($search); // SELECT * FROM `post` WHERE title LIKE :search;
        }

        return $this->render('post/index.html.twig', [
            "posts" => $posts
        ]);
    }

    #[Route('/post/new')]
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

            }
            return $this->redirectToRoute("home");
        
        return $this->render('post/form.html.twig', [
            "PostForm" => $PostForm->createView()
        ]);
    }

    #[Route('/post/edit/{id}', name: "edit-post")]
    public function update(Post $post, Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $post->getUser()) {
          $this->addFlash("error", "Vous ne pouvez pas modifier une publication qui ne vous appartient pas.");
          return $this->redirectToRoute("home");
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

            return $this->redirectToRoute("home");
        }
        return $this->render('post/form.html.twig', [
            "PostForm" => $PostForm->createView(),
            'post' => $post
        ]);
    }

    #[Route('/post/delete/{id}', name: "delete-post")]
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $post->getUser()) {
          $this->addFlash("error", "Vous ne pouvez pas supprimer une publication qui ne vous appartient pas.");
          return $this->redirectToRoute("home");
        }
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post supprimé avec succès');
        return $this->redirectToRoute("home");
    }

}