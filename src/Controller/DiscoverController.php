<?php

namespace App\Controller;

use App\Entity\Discover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\DiscoverRepository;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\Annotation\Route;

class DiscoverController extends AbstractController
{
    #[Route('/decouverte', name: 'discover')]
    public function index(Discover $discvocer, DiscoverRepository $discoverRepository, ImageRepository $imageRepository): Response
    {
        return $this->render('discover/index.html.twig',[
          'discover' => $discoverRepository->findAll(),
          'image' => $imageRepository->findAll(),
        ]);
    }
}
