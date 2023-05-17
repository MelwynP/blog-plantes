<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(PostRepository $postRepository, ArticleRepository $articleRepository, ImageRepository $imageRepository): Response
    {

        return $this->render('main/index.html.twig', [
      'post' => $postRepository->findAll(),
      'article' => $articleRepository->findAll(),
      'images' => $imageRepository->findAll(),
        ]);
    }
}
