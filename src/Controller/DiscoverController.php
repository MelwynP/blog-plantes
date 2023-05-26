<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DiscoverRepository;
use App\Repository\ImageRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;

class DiscoverController extends AbstractController
{
    #[Route('/decouverte', name: 'discover')]
    public function index(DiscoverRepository $discoverRepository, ImageRepository $imageRepository, ArticleRepository $articleRepository): Response
    {
        return $this->render('discover/index.html.twig',[
          'discover' => $discoverRepository->findAll(),
          'image' => $imageRepository->findAll(),
          'article' => $articleRepository->findAll(),
        ]);
    }
}
