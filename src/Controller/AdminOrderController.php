<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/employee/order')]
class AdminOrderController extends AbstractController
{
    #[Route('/', name: 'app_admin_order_index', methods: ['GET'])]
    public function index(
        OrderRepository $orderRepository,
        PaginatorInterface $paginator,
        Request $request,
        #[CurrentUser] ?User $user,
        UserRepository $userRepository
    ): Response {
        $pagination = null;

        if ($this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur est un admin, récupérer toutes les commandes
            $pagination = $paginator->paginate(
                $orderRepository->findAll(),
                $request->query->getInt('page', 1), // numéro de page
                10 // nombre d'éléments par page
            );
        } elseif ($this->isGranted('ROLE_EMPLOYEE') && $user) {
            // Si l'utilisateur est un employé, récupérer les commandes de ses clients
            $clients = $userRepository->findBy(['employee' => $user]);

            // Récupérer les commandes des clients trouvés
            $clientIds = [];
            foreach ($clients as $client) {
                $clientIds[] = $client->getId();
            }

            $pagination = $paginator->paginate(
                $orderRepository->findBy(['user' => $clientIds]),
                $request->query->getInt('page', 1), // numéro de page
                10 // nombre d'éléments par page
            );
        }

        return $this->render('admin_order/index.html.twig', [
            'orders' => $pagination,
        ]);
    }

   

    #[Route('/{id}', name: 'app_admin_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin_order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
