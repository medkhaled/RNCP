<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class ApiLoginController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST', 'OPTIONS'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Veuillez vérifier vos identifiants',
            ], Response::HTTP_UNAUTHORIZED);
        }

        
        $context = [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ];

        // Serialize only the necessary fields
        $userData = [
            'email'     => $user->getEmail(),
            'firstName' => $user->getFirstname(),
            'lastName'  => $user->getLastname(),
            'address'   => $user->getAddress(),
            'zipcode'   => $user->getZipcode(),
            'city'      => $user->getCity(),
            'employee'  => $user->getEmployee() ? $user->getEmployee()->getFirstname() : null, 
        ];

        return new JsonResponse($userData, Response::HTTP_OK);
    }
}
