<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;


#[Route('/cart' , name:'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name:'index')] 
    public function index( SessionInterface $session,ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);
        //  On initialise des variables pour récupérer les infos des produits
        $data = [];
        $total = 0;
        foreach ($panier as $id =>$quantity){
            $product = $productRepository->find($id);
            $data[$id] = [
                    'product' => $product,
                    'quantity' => $quantity
            ];
            $total+= $product->getPrice() * $quantity;
        }
        return $this->render('cart/index.html.twig',compact('data','total'));
    }

    #[Route('/add/{id}', name:'add')] 
    public function add(Product $product, SessionInterface $session)
    {
    //  On récupere l'id du produit 
        $id= $product->getId();

    //  On récupere le panier existant 
        $panier = $session->get('panier',[]);

    //  On ajoute le produit dans le panier s'il n'y est pas
    //  sinon on incremente  sa quantité
        if (empty($panier[$id])){
            $panier[$id] = 1 ;
        }else{
            $panier[$id]++;
        }
        $session->set('panier', $panier);
    //  On redirige vers la page du panier en cas d'ajourt de produit
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name:'remove')] 
    public function remove(Product $product, SessionInterface $session)
    {
    //  On récupere l'id du produit 
        $id= $product->getId();

    //  On récupere le panier existant 
        $panier = $session->get('panier',[]);

    //  On va retirer  le produit du panier s'il n'y a qu'un exemplaire
    //  sinon on décremente  sa quantité
        if (!empty($panier[$id])){
            if($panier[$id]>1){
            $panier[$id] -- ;
        }else{
            unset($panier[$id]);
        }
    }
        $session->set('panier', $panier);
    //  On redirige vers la page du panier en cas d'ajourt de produit
        return $this->redirectToRoute('cart_index');
    }
    #[Route('/delete/{id}', name:'delete')] 
    public function delete(Product $product, SessionInterface $session)
    {
    //  On récupere l'id du produit 
        $id= $product->getId();

    //  On récupere le panier existant 
        $panier = $session->get('panier',[]);

//      On verifie qu'on a bien quelquechose dans le panier
        if (!empty($panier[$id])){
            unset($panier[$id]);
        }
    
        $session->set('panier', $panier);
    //  On redirige vers la page du panier en cas d'ajourt de produit
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/empty', name:'empty')] 
    public function empty( SessionInterface $session)
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart_index');
    }
}