<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Form\CategoryForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/category', name: 'admin_category_')]

class CategoryController extends AbstractController
{
  #[Route('/', name: 'index')]

  public function index(CategoryRepository $categoryRepository): Response
  {
    $category = $categoryRepository->findAll();
    return $this->render('admin/category/index.html.twig', compact('category'));
  }

  #[Route('/ajouter', name: 'add')]

  public function add(Request $request, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    //On crée un nouvel objet Menu
    $category = new Category();

    //On crée le formulaire
    $categoryFormulaire = $this->createForm(CategoryForm::class, $category);

    //On traite la requête du formulaire
    $categoryFormulaire->handleRequest($request);

    if ($categoryFormulaire->isSubmitted() && $categoryFormulaire->isValid()) {


      $cty = new Category();
      $cty->setName($categoryFormulaire->get('name')->getData());
     

      $em->persist($cty);
      $em->flush();

      $this->addFlash('success', 'Categorie ajouté avec succès');

      // On redirige vers la liste des photos
      return $this->redirectToRoute('admin_category_index');
    }

    return $this->render('admin/category/add.html.twig', [
      'categoryFormulaire' => $categoryFormulaire->createView()
    ]);
  }


  #[Route('/modifier/{id}', name: 'edit')]
  public function edit(Request $request, EntityManagerInterface $em, $id): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // On récupère le menu à modifier
    $category = $em->getRepository(Category::class)->find($id);

    // On crée le formulaire en passant l'objet categorie à modifier
    $categoryFormulaire = $this->createForm(CategoryForm::class, $category);

    // On traite la requête du formulaire
    $categoryFormulaire->handleRequest($request);

    //On vérifie si le formulaire est soumis ET valide
    if ($categoryFormulaire->isSubmitted() && $categoryFormulaire->isValid()) {

      // On met à jour les données du menu
      $category->setName($categoryFormulaire->get('name')->getData());
      

      $em->flush();

      $this->addFlash('success', 'Categorie modifié avec succès');

      // On redirige vers la liste des categorie
      return $this->redirectToRoute('admin_category_index');
    }

    return $this->render('admin/category/add.html.twig', [
      'categoryFormulaire' => $categoryFormulaire->createView()
    ]);
  }

  #[Route('/supprimer/{id}', name: "delete")]
  public function delete(Category $category, EntityManagerInterface $em): Response
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    try {
      // Récupérer les plats associés à ce menu
      $article = $category->getArticles();
      foreach ($article as $article) {
        $article->setCategory(null);
        $em->persist($article);
      }
      $em->remove($category);
      $em->flush();
      $this->addFlash(
        'success',
        'Categorie supprimé avec succès'
      );
    } catch (\Exception $e) {
      $this->addFlash(
        'danger',
        'Impossible de supprimer la categorie. Elle est probablement utilisé avec d\'autres article. Supprimer ou basculer ces articles avant de supprimer la categorie.'
      );
    }

    return $this->redirectToRoute("admin_category_index");
  }
}
