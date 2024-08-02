<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->query->get('category');
        $categories = $categoryRepository->findAll();

        if ($categoryId) {
            $products = $productRepository->findBy(['category' => $categoryId]);
        } else {
            $products = $productRepository->findAll();
        }

        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'CategoryController',
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
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

