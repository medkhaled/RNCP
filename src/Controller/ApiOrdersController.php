<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api/orders')]
class ApiOrdersController extends AbstractController
{
    
    #[Route('/', name: 'order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, UserInterface $user): JsonResponse
    {
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
                'id' => $order->getId(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'total' => $order->getTotal(),
                'status' => $order->getStatus(),
                'items' => $items,
            ];
        }

        return new JsonResponse($data);
    }
}
