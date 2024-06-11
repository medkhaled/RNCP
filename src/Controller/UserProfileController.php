<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEdit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UserProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Utiliser getUser() pour obtenir l'utilisateur actuel
        $user = $this->getUser();
        //dump($user);

        //if (!$user instanceof User) {
        //    $this->addFlash('error', 'Utilisateur non trouvé ou non connecté.');
        //    return $this->redirectToRoute('app_login');
        //}

        $form = $this->createForm(UserEdit::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos données sont enregistrées');

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user_profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
