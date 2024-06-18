<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
    #[Route('/boutique/{id}', name: 'app_boutique_show')]
    public function show(Category $category): Response
    {
        return $this->render('boutique/show.html.twig', [
            'categorie' => $category
        ]);
    }
    #[Route('/boutique/product/{id}', name: 'product_detail')]
    public function showProduct(Product $product): Response
    {
        return $this->render('boutique/detail.html.twig', [
            'product' => $product,
        ]);
    }
}
