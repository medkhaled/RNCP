<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST','OPTIONS'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
                         return $this->json([
                             'message' => 'Veuillez vérifier vos identifiants',
                         ], Response::HTTP_UNAUTHORIZED);
                     }
            
        
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            
        ]);
    }
}
