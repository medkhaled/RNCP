<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
#[Route('/user')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request,Security $security): Response
{
    if ($this->isGranted('ROLE_ADMIN')) {
    $pagination = $paginator->paginate(
        
        $userRepository->findAll(),
        $request->query->getInt('page', 1), // numéro de page
        10 // nombre d'éléments par page
    );
        }elseif($this->isGranted('ROLE_EMPLOYEE')){
             $currentUser = $this->getUser();
              if (!$currentUser || !$currentUser instanceof User) {
                return $this->redirectToRoute('app_login');
              }
            $userId = $currentUser->getId();          
            $pagination = $paginator->paginate(
                $userRepository->findByEmployeeId($userId),
                
                $request->query->getInt('page', 1), // numéro de page
                10 // nombre d'éléments par page
            );
            }
    return $this->render('admin/index.html.twig', [
        'pagination' => $pagination,
    ]);
}
    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,UserPasswordHasherInterface $userPasswordHasher,UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $role = $user->getRoles();
            $id = $user->getId();
            // dd($id, $role);
            if ($this->isGranted('ROLE_ADMIN')) {
                if  (in_array('ROLE_EMPLOYEE', $role)){
                    $employee = $entityManager->getReference('App\Entity\User', $id);
                    $user->setMatricule(substr($user->getFirstname(),0,2).substr($user->getLastname(),0,2).$id);
                }elseif(in_array('ROLE_USER', $role)) {
                    $employeeId = $userRepository->findEmployeeIdWithMinUserCount();
                    $employeeId = $employeeId[0]->getEmployeeid()->getId();
                    $employee = $entityManager->getReference('App\Entity\User', $employeeId);     
                    
                }
            }else {
            // Forcer le rôle ROLE_USER
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            $employeeId = $userRepository->findEmployeeIdWithMinUserCount();
            $employeeId = $employeeId[0]->getEmployeeid()->getId();
            $employee = $entityManager->getReference('App\Entity\User', $employeeId);
            }
            $user->setEmployeeid($employee);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher,): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
