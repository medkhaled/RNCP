<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/message')]
class MessageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/conversation', name: 'message_conversation', methods: ['GET', 'POST'])]
    public function userConversation(#[CurrentUser] ?User $user, MessageRepository $messageRepository, Request $request): Response
    {
        if (in_array('ROLE_USER', $user->getRoles())) {
            $employee = $user->getEmployee();
            if (!$employee) {
                throw $this->createNotFoundException('Employee not found');
            }

            $messages = $messageRepository->findConversation($user, $employee);

            $message = new Message();
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $message->setSender($user);
                $message->setRecipient($employee);
                $cleanedContent = htmlspecialchars($message->getContent(), ENT_QUOTES, 'UTF-8');
                $message->setContent($cleanedContent);

                $this->entityManager->persist($message);
                $this->entityManager->flush();

                return $this->redirectToRoute('message_conversation');
            }

            return $this->render('message/conversation.html.twig', [
                'messages' => $messages,
                'user' => $employee,
                'form' => $form->createView(),
            ]);
        }

        throw $this->createAccessDeniedException();
    }

    #[Route('/conversation/{userId}', name: 'message_conversation_employee', methods: ['GET', 'POST'])]
    public function employeeConversation(#[CurrentUser] ?User $employee, UserRepository $userRepository, MessageRepository $messageRepository, Request $request, int $userId): Response
    {
        if ($this->isGranted('ROLE_EMPLOYEE')) {
            $user = $userRepository->find($userId);
            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }

            $messages = $messageRepository->findConversation($user, $employee);

            $message = new Message();
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $message->setSender($employee);
                $message->setRecipient($user);

                $this->entityManager->persist($message);
                $this->entityManager->flush();

                return $this->redirectToRoute('message_conversation_employee', ['userId' => $userId]);
            }

            return $this->render('message/conversation.html.twig', [
                'messages' => $messages,
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }

        throw $this->createAccessDeniedException();
    }

    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $messages = $messageRepository->findBy(['recipient' => $user]);

        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }



    #[Route('/{id}', name: 'message_read', methods: ['GET'])]
    public function show(Message $message): Response
    {
        $message->setIsRead('true');
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('message_index');
    }
}
