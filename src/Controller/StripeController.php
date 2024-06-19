<?php
 
namespace App\Controller;
 
use Stripe;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderItem;
use Stripe\Exception\CardException;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }
 
 
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(SessionInterface $session,Request $request,ProductRepository $productRepository, EntityManagerInterface $entityManager,#[CurrentUser] ?User $user)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $total=$session->get('total');
        $panier = $session->get('panier', []);
        if ($total){
            try {
                Stripe\Charge::create ([
                        "amount" => $total*100,
                        "currency" => "eur",
                        "source" => $request->request->get('stripeToken'),
                        "description" => "VentaliShop"
                ]);
                $this->addFlash(
                    'success',
                    'Payment réussie'
                );
                $order = new Order();
                
                $order->setUser($user);
                $order->setStatus("paid");
                $order->setTotal($total);
                $order->setOrderCode(substr($user->getFirstname(),0,2).substr($user->getLastname(),0,2));
                foreach ($panier as $id => $quantity) {
                    $product = $productRepository->find($id);
                    if ($product) {
                        $orderItem = new OrderItem();
                        $orderItem->setProduct($product);
                        $orderItem->setQuantity($quantity);
                        $orderItem->setPrice($product->getPrice()*$quantity);
                        $orderItem->setOrders($order);
                        $order->getItems()->add($orderItem);
                    
                    }
                }
                $entityManager->persist($order);
                $entityManager->flush();
                $order->setOrderCode(substr($user->getFirstname(),0,2).substr($user->getLastname(),0,2).$order->getId());
                $entityManager->persist($order);
                $entityManager->flush();
                $session->remove('panier');
                $session->remove('total');
            } catch (CardException $e) {
                // Payment failed
                $this->addFlash('error', 'Payment échoué: ' . $e->getMessage());
            } catch (\Exception $e) {
                // Other errors
                $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
            }
        }else{
            $this->addFlash('error', 'Panier Vide ' );
        }

        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    }
}
