<?php

namespace App\Controller;

use App\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/confirmation/{id}', name: 'app_order_confirmation')]
    public function confirmation(Invoice $invoice): Response
    {
        // Vérifier si l'utilisateur est le propriétaire de la facture
        if ($this->getUser() !== $invoice->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette commande.');
        }
        
        return $this->render('order/confirmation.html.twig', [
            'invoice' => $invoice,
        ]);
    }
} 