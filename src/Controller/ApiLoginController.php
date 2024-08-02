<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiLoginController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST', 'OPTIONS'])]
    public function index(#[CurrentUser] ?User $user, OrderRepository $orderRepository, MessageRepository $messageRepository): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Veuillez vérifier vos identifiants',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $context = [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
            'groups' => ['user:read'],
        ];

        $employee = $user->getEmployee();
        $userData = [
            'id' => $user->getId(),
            'Email' => $user->getEmail(),
            'Prénom' => $user->getFirstname(),
            'Nom' => $user->getLastname(),
            'Addresse' => $user->getAddress(),
            'Code Postal' => $user->getZipcode(),
            'Ville' => $user->getCity(),
        ];
        $employeeData =[
            'Prénom' => $employee ? $employee->getFirstname() : null,
            'Nom' => $employee ? $employee->getLastname() : null,
            'Email' => $employee ? $employee->getEmail() : null,
        ];
        $orders = $orderRepository->findBy(['user' => $user]);
        $orderData = [];

        foreach ($orders as $order) {
            $items = [];
            foreach ($order->getItems() as $item) {
                $items[] = [
                    'product' => $item->getProduct()->getName(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                ];
            }
            $orderData[] = [
                'orderCode' => $order->getOrderCode(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'total' => $order->getTotal(),
                'status' => $order->getStatus(),
                'items' => $items,
            ];
        }

        $messages = $messageRepository->findConversation($user, $employee);
        $userMessage = [];
        foreach ($messages as $message) {
            $userMessage[] = [
                'sender' => $message->getSender()->getId(),
                'recipient' => $message->getRecipient()->getId(),
                'date' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'content' => $message->getContent(),
            ];
        }

        $responseData = [
            'user' => $userData,
            'employee' => $employeeData,
            'orders' => $orderData,
            'messages' => $userMessage,
        ];

        return $this->json($responseData, Response::HTTP_OK, [], $context);
    }
}
