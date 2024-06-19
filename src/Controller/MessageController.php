<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

#[Route('/message')]
class MessageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

    #[Route('/new', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] ?User $user,Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $employee=$user->getEmployee();
            $message->setSender($user);
            $message->setRecipient($employee);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        if ($this->getUser() !== $message->getRecipient() && $this->getUser() !== $message->getSender()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }
}
