<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Watch;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

final class CartController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $cartItems = [];
        $total = 0;
        
        // Si l'utilisateur est connecté, récupérer son panier depuis la base de données
        if ($this->getUser()) {
            $cartItems = $entityManager->getRepository(CartItem::class)->findBy([
                'user' => $this->getUser(), 
                'invoice' => null
            ]);
            
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
                $newQuantity = $cartItem->getQuantity() + $quantity;
                
                // Vérifier si la nouvelle quantité est disponible
                if ($watch->getStock()->getWatchStock() < $newQuantity) {
                    $this->addFlash('error', 'La quantité totale demandée n\'est pas disponible.');
                    return $this->redirectToRoute('app_watch_details', ['id' => $watch->getId()]);
                }
                
                $cartItem->setQuantity($newQuantity);
            } else {
                // Créer un nouvel élément de panier
                $cartItem = new CartItem();
                $cartItem->setUser($this->getUser());
                $cartItem->setWatch($watch);
                $cartItem->setQuantity($quantity);
                
                $entityManager->persist($cartItem);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Article ajouté au panier avec succès.');
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
            
            $this->addFlash('success', 'Article ajouté au panier avec succès.');
        }
        
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
        
        $this->addFlash('success', 'Article supprimé du panier.');
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/update/{id}', name: 'app_cart_update')]
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

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->logger->info('Début de la méthode checkout');
        
        // 1. Vérifier l'authentification
        if (!$this->getUser()) {
            $this->logger->error('Utilisateur non connecté');
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour finaliser votre commande.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        // 2. Récupérer et valider les données
        $content = $request->getContent();
        $this->logger->info('Contenu de la requête: ' . $content);
        
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $this->logger->info('Données décodées: ' . print_r($data, true));
        } catch (\JsonException $e) {
            $this->logger->error('Erreur de décodage JSON: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Format de données invalide: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 3. Valider les champs requis
        $requiredFields = ['address', 'postalCode', 'city', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->logger->error('Champ manquant: ' . $field);
                return $this->json([
                    'success' => false,
                    'message' => 'Le champ ' . $field . ' est requis.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        
        // 4. Récupérer les articles du panier
        $user = $this->getUser();
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy([
            'user' => $user,
            'invoice' => null
        ]);
        
        if (empty($cartItems)) {
            $this->logger->error('Panier vide');
            return $this->json([
                'success' => false,
                'message' => 'Votre panier est vide.'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 5. Calculer le total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->getWatch()->getPrice() * $item->getQuantity();
        }
        $this->logger->info('Total calculé: ' . $total);
        
        // 6. Vérifier le solde
        if ($user->getBalance() < $total) {
            $this->logger->error('Solde insuffisant: ' . $user->getBalance() . ' < ' . $total);
            return $this->json([
                'success' => false,
                'message' => 'Solde insuffisant pour effectuer cet achat.'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 7. Créer la facture et traiter la commande
        try {
            $entityManager->beginTransaction();
            $this->logger->info('Début de la transaction');
            
            // Créer la facture
            $invoice = new Invoice();
            $invoice->setUser($user);
            $invoice->setAmount($total);
            $invoice->setStatus('Payée');
            $invoice->setCreatedAt(new \DateTime());
            $invoice->setAddress($data['address']);
            $invoice->setPostalCode($data['postalCode']);
            $invoice->setCity($data['city']);
            $invoice->setPhone($data['phone']);
            
            $entityManager->persist($invoice);
            $this->logger->info('Facture créée');
            
            // Traiter les articles
            foreach ($cartItems as $item) {
                // Vérifier le stock
                $stock = $item->getWatch()->getStock();
                $currentStock = $stock->getWatchStock();
                $requestedQuantity = $item->getQuantity();
                
                if ($currentStock < $requestedQuantity) {
                    throw new \Exception('Stock insuffisant pour ' . $item->getWatch()->getName() . 
                        '. Stock disponible: ' . $currentStock . ', Quantité demandée: ' . $requestedQuantity);
                }
                
                // Mettre à jour le stock
                $stock->setWatchStock($currentStock - $requestedQuantity);
                
                // Associer l'article à la facture
                $item->setInvoice($invoice);
                
                $this->logger->info('Article traité: ' . $item->getWatch()->getName() . 
                    ', Quantité: ' . $requestedQuantity . 
                    ', Nouveau stock: ' . ($currentStock - $requestedQuantity));
            }
            
            // Mettre à jour le solde de l'utilisateur
            $newBalance = $user->getBalance() - $total;
            $user->setBalance($newBalance);
            $this->logger->info('Solde mis à jour: ' . $user->getBalance() . ' -> ' . $newBalance);
            
            // Sauvegarder les changements
            $entityManager->flush();
            $entityManager->commit();
            $this->logger->info('Transaction validée');
            
            return $this->json([
                'success' => true,
                'message' => 'Commande validée avec succès !',
                'invoiceId' => $invoice->getId()
            ]);
            
        } catch (\Exception $e) {
            $entityManager->rollback();
            $this->logger->error('Erreur lors de la validation: ' . $e->getMessage());
            $this->logger->error('Trace: ' . $e->getTraceAsString());
            
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/cart/delivery', name: 'app_cart_delivery')]
    public function delivery(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy([
            'user' => $this->getUser(),
            'invoice' => null
        ]);
        
        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->getWatch()->getPrice() * $item->getQuantity();
        }
        
        // Vérifier si l'utilisateur a assez d'argent
        if ($this->getUser()->getBalance() < $total) {
            $this->addFlash('error', 'Solde insuffisant pour effectuer cet achat.');
            return $this->redirectToRoute('app_cart');
        }
        
        // Récupérer la dernière facture de l'utilisateur pour pré-remplir les champs
        $lastInvoice = $entityManager->getRepository(Invoice::class)
            ->findOneBy(
                ['user' => $this->getUser()],
                ['createdAt' => 'DESC']
            );
        
        return $this->render('cart/delivery.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
            'lastInvoice' => $lastInvoice
        ]);
    }
}
