<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/admin/post', name: 'admin_post_')]
class PostCheckController extends AbstractController
{
  #[Route('/', name: 'index')]

  public function index(PostRepository $postRepository, EntityManagerInterface $em): Response
  {
    $postOrder = $em->getRepository(Post::class)
      ->createQueryBuilder('b')
      ->orderBy('b.publishedAt', 'ASC')
      ->getQuery()
      ->getResult();

    return $this->render('admin/post/index.html.twig', [
      'post' => $postRepository->findAll(),
      'postOrder' => $postOrder,
    ]);
  }


  #[Route('/supprimer/{id}', name: "delete")]
  #[ParamConverter("post", class:"App\Entity\post")]

  public function delete(Post $post, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $em->remove($post);
    $em->flush();

    $this->addFlash(
      'success',
      'Post supprimé avec succès !'
    );

    return $this->redirectToRoute("admin_post_index");
  }

}
