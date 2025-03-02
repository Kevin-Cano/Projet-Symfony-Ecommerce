<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Watch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Invoice;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $cartItems = [];
        $total = 0;
        
        // Si l'utilisateur est connecté, récupérer son panier depuis la base de données
        if ($this->getUser()) {
            $cartItems = $entityManager->getRepository(CartItem::class)->findBy(['user' => $this->getUser(), 'invoice' => null]);
            
            // Calculer le total
            foreach ($cartItems as $item) {
                $total += $item->getWatch()->getPrice() * $item->getQuantity();
            }
        } else {
            // Sinon, récupérer le panier depuis la session
            $cart = $session->get('cart', []);
            
            if (!empty($cart)) {
                $watchRepository = $entityManager->getRepository(Watch::class);
                
                foreach ($cart as $id => $quantity) {
                    $watch = $watchRepository->find($id);
                    if ($watch) {
                        $cartItems[] = [
                            'watch' => $watch,
                            'quantity' => $quantity
                        ];
                        $total += $watch->getPrice() * $quantity;
                    }
                }
            }
        }
        
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }
    
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(Watch $watch, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        // Vérifier si la quantité demandée est disponible
        if ($watch->getStock()->getWatchStock() < $quantity) {
            $this->addFlash('error', 'La quantité demandée n\'est pas disponible.');
            return $this->redirectToRoute('app_watch_details', ['id' => $watch->getId()]);
        }
        
        // Si l'utilisateur est connecté, ajouter au panier en base de données
        if ($this->getUser()) {
            // Vérifier si l'article est déjà dans le panier
            $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
                'user' => $this->getUser(),
                'watch' => $watch,
                'invoice' => null
            ]);
            
            if ($cartItem) {
                // Mettre à jour la quantité
                $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
            } else {
                // Créer un nouvel élément de panier
                $cartItem = new CartItem();
                $cartItem->setUser($this->getUser());
                $cartItem->setWatch($watch);
                $cartItem->setQuantity($quantity);
                $entityManager->persist($cartItem);
            }
            
            $entityManager->flush();
        } else {
            // Sinon, ajouter au panier en session
            $cart = $session->get('cart', []);
            $id = $watch->getId();
            
            if (isset($cart[$id])) {
                $cart[$id] += $quantity;
            } else {
                $cart[$id] = $quantity;
            }
            
            $session->set('cart', $cart);
        }
        
        $this->addFlash('success', 'Article ajouté au panier avec succès.');
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(Watch $watch, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Si l'utilisateur est connecté, supprimer de la base de données
        if ($this->getUser()) {
            $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
                'user' => $this->getUser(),
                'watch' => $watch,
                'invoice' => null
            ]);
            
            if ($cartItem) {
                $entityManager->remove($cartItem);
                $entityManager->flush();
            }
        } else {
            // Sinon, supprimer de la session
            $cart = $session->get('cart', []);
            $id = $watch->getId();
            
            if (isset($cart[$id])) {
                unset($cart[$id]);
                $session->set('cart', $cart);
            }
        }
        
        $this->addFlash('success', 'Article retiré du panier.');
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(Watch $watch, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        
        // Vérifier si la quantité est valide
        if ($quantity <= 0) {
            return $this->redirectToRoute('app_cart_remove', ['id' => $watch->getId()]);
        }
        
        // Vérifier si la quantité demandée est disponible
        if ($watch->getStock()->getWatchStock() < $quantity) {
            $this->addFlash('error', 'La quantité demandée n\'est pas disponible.');
            return $this->redirectToRoute('app_cart');
        }
        
        // Si l'utilisateur est connecté, mettre à jour en base de données
        if ($this->getUser()) {
            $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
                'user' => $this->getUser(),
                'watch' => $watch,
                'invoice' => null
            ]);
            
            if ($cartItem) {
                $cartItem->setQuantity($quantity);
                $entityManager->flush();
            }
        } else {
            // Sinon, mettre à jour en session
            $cart = $session->get('cart', []);
            $id = $watch->getId();
            
            if (isset($cart[$id])) {
                $cart[$id] = $quantity;
                $session->set('cart', $cart);
            }
        }
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout')]
    public function checkout(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour finaliser votre commande.');
            return $this->redirectToRoute('app_login');
        }
        
        $user = $this->getUser();
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy(['user' => $user, 'invoice' => null]);
        
        // Vérifier si le panier est vide
        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }
        
        // Vérifier la disponibilité des stocks
        $stockError = false;
        foreach ($cartItems as $item) {
            $watch = $item->getWatch();
            if ($watch->getStock()->getWatchStock() < $item->getQuantity()) {
                $this->addFlash('error', 'Le produit "' . $watch->getName() . '" n\'est plus disponible en quantité suffisante.');
                $stockError = true;
            }
        }
        
        if ($stockError) {
            return $this->redirectToRoute('app_cart');
        }
        
        // Créer une nouvelle facture
        $invoice = new Invoice();
        $invoice->setUser($this->getUser());
        
        // Utiliser l'adresse de l'utilisateur si disponible
        if ($user->getDeliveryAddress() && $user->getPostalCode()) {
            $invoice->setAddress($user->getDeliveryAddress());
            $invoice->setPostalCode($user->getPostalCode());
            $invoice->setCity('Ville'); // À améliorer
        } else {
            // Rediriger vers un formulaire de saisie d'adresse si nécessaire
            return $this->redirectToRoute('app_validate');
        }
        
        $entityManager->persist($invoice);
        
        // Mettre à jour les éléments du panier
        $total = 0;
        foreach ($cartItems as $item) {
            $watch = $item->getWatch();
            
            // Mettre à jour le stock
            $stock = $watch->getStock();
            $stock->setWatchStock($stock->getWatchStock() - $item->getQuantity());
            
            // Associer l'élément à la facture
            $item->setInvoice($invoice);
            
            // Calculer le total
            $total += $watch->getPrice() * $item->getQuantity();
        }
        
        $entityManager->flush();
        
        // Vider le panier en session
        $session->remove('cart');
        
        $this->addFlash('success', 'Votre commande a été validée avec succès!');
        return $this->redirectToRoute('app_order_confirmation', ['id' => $invoice->getId()]);
    }
}
