<?php

namespace App\Controller;

use App\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InvoiceController extends AbstractController
{
    #[Route('/invoice/{id}', name: 'app_invoice_show')]
    public function show(Invoice $invoice): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        // Vérifier si l'utilisateur est le propriétaire de la facture
        if ($this->getUser() !== $invoice->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à voir cette facture.');
        }
        
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice
        ]);
    }
} 