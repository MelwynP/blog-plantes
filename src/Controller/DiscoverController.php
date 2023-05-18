<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscoverController extends AbstractController
{
    #[Route('/decouverte', name: 'discover')]
    public function index(): Response
    {
        return $this->render('discover/index.html.twig');
    }
}
