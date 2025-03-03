<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $user = $this->getUser();
        
        // Traiter le formulaire d'adresse
        if ($request->isMethod('POST')) {
            $address = $request->request->get('address');
            $postalCode = $request->request->get('postal_code');
            $city = $request->request->get('city');
            
            if ($address && $postalCode && $city) {
                // Mettre à jour l'adresse de l'utilisateur
                $user->setDeliveryAddress($address);
                $user->setPostalCode($postalCode);
                
                $entityManager->flush();
                
                // Rediriger vers la validation du panier
                return $this->redirectToRoute('app_cart_checkout');
            }
        }
        
        return $this->render('validate/index.html.twig', [
            'user' => $user,
        ]);
    }
}
