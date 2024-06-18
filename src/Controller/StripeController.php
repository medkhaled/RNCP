<?php
 
namespace App\Controller;
 
use Stripe;
use Stripe\Exception\CardException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function createCharge(SessionInterface $session,Request $request)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $total=$session->get('total');
        
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
