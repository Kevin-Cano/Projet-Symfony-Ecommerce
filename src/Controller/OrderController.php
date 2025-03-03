<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/confirmation/{id}', name: 'app_order_confirmation')]
    public function confirmation(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur connecté est bien le propriétaire de la facture
        if (!$this->getUser() || $invoice->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette commande.');
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('order/confirmation.html.twig', [
            'invoice' => $invoice
        ]);
    }
} 