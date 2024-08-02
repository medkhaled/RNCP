<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OrderController extends AbstractController
{
    

   
    #[Route('/order', name: 'app_order')]
    public function index(#[CurrentUser] ?User $user, OrderRepository $orderRepository): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos commandes.');
        }
    
        $orders = $orderRepository->findBy(['user' => $user]);
        $data = [];
    
        foreach ($orders as $order) {
            $items = [];
            foreach ($order->getItems() as $item) {
                $items[] = [
                    'product' => $item->getProduct()->getName(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                ];
            }
            $data[] = [
                'orderCode' => $order->getOrderCode(),
                'createdAt' => $order->getCreatedAt(),
                'total' => $order->getTotal(),
                'status' => $order->getStatus(),
                'items' => $items,
            ];
        }
    
        return $this->render('order/index.html.twig', [
            'orders' => $data, // Passez les données au template
        ]);
    }
    
    
}
