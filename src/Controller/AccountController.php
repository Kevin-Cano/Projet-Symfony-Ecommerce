<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AccountController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        Security $security, 
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/account', name: 'app_account')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            // Rediriger vers la page de connexion si non connecté
            return $this->redirectToRoute('app_login');
        }
        
        // Charger l'utilisateur avec toutes ses relations
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        
        // Initialiser les collections pour éviter les erreurs
        $watches = $user->getWatches()->toArray();
        $invoices = $user->getInvoices()->toArray();
        $favorites = $user->getFavorites()->toArray();
        
        // Passer l'utilisateur connecté au template
        return $this->render('account/index.html.twig', [
            'user' => $user,
            'watches' => $watches,
            'invoices' => $invoices,
            'favorites' => $favorites,
        ]);
    }

    #[Route('/account/add-funds', name: 'app_account_add_funds', methods: ['POST'])]
    public function addFunds(Request $request): Response
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $amount = $request->request->get('amount');

        // Validation du montant
        $constraints = new Assert\Collection([
            'amount' => [
                new Assert\NotBlank(['message' => 'Le montant ne peut pas être vide.']),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'Le montant doit être un nombre.'
                ]),
                new Assert\GreaterThan([
                    'value' => 0,
                    'message' => 'Le montant doit être supérieur à 0.'
                ]),
                new Assert\LessThanOrEqual([
                    'value' => 10000,
                    'message' => 'Le montant ne peut pas dépasser 10000.'
                ])
            ]
        ]);

        $violations = $this->validator->validate(['amount' => $amount], $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return $this->json([
                'success' => false,
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $currentBalance = $user->getBalance() ?? 0;
            $user->setBalance($currentBalance + floatval($amount));
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Fonds ajoutés avec succès !',
                'newBalance' => $user->getBalance()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout des fonds.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-phone', name: 'app_account_update_phone', methods: ['POST'])]
    public function updatePhone(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $phoneNumber = $request->request->get('phoneNumber');

        if (!$phoneNumber) {
            return $this->json([
                'success' => false,
                'message' => 'Le numéro de téléphone est requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match('/^\+?[0-9]{10,15}$/', $phoneNumber)) {
            return $this->json([
                'success' => false,
                'message' => 'Format de numéro de téléphone invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setPhoneNumber($phoneNumber);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Numéro de téléphone mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-password', name: 'app_account_update_password', methods: ['POST'])]
    public function updatePassword(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $currentPassword = $request->request->get('currentPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        // Vérification du mot de passe actuel
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validation du nouveau mot de passe
        if (strlen($newPassword) < 8) {
            return $this->json([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->json([
                'success' => false,
                'message' => 'Les mots de passe ne correspondent pas.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/delete-account', name: 'app_account_delete_account', methods: ['POST'])]
    public function deleteAccount(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        try {
            // Récupérer l'EntityManager
            $em = $this->entityManager;

            // Commencer une transaction
            $em->beginTransaction();

            try {
                // Supprimer l'utilisateur (et toutes ses relations en cascade)
                $em->remove($user);
                $em->flush();

                // Si tout s'est bien passé, valider la transaction
                $em->commit();

                // Déconnexion de l'utilisateur
                $this->security->logout(false);

                return $this->json([
                    'success' => true,
                    'message' => 'Compte et données associées supprimés avec succès.'
                ]);
            } catch (\Exception $e) {
                // En cas d'erreur, annuler la transaction
                $em->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du compte.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-personal-info', name: 'app_account_update_personal_info', methods: ['POST'])]
    public function updatePersonalInfo(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $field = $request->request->get('field');
        $value = $request->request->get('value');

        // Validation des champs
        $allowedFields = ['firstName', 'lastName', 'deliveryAddress', 'postalCode'];
        if (!in_array($field, $allowedFields)) {
            return $this->json([
                'success' => false,
                'message' => 'Champ non autorisé.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Mise à jour du champ
            switch ($field) {
                case 'firstName':
                    $user->setFirstName($value);
                    break;
                case 'lastName':
                    $user->setLastName($value);
                    break;
                case 'deliveryAddress':
                    $user->setDeliveryAddress($value);
                    break;
                case 'postalCode':
                    if (!preg_match('/^\d{5}$/', $value)) {
                        return $this->json([
                            'success' => false,
                            'message' => 'Code postal invalide.'
                        ], Response::HTTP_BAD_REQUEST);
                    }
                    $user->setPostalCode($value);
                    break;
            }

            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Information mise à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-username', name: 'app_account_update_username', methods: ['POST'])]
    public function updateUsername(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $newUsername = $request->request->get('userName');

        // Validation basique
        if (empty($newUsername)) {
            return $this->json([
                'success' => false,
                'message' => 'Le nom d\'utilisateur ne peut pas être vide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Vérifier si le nom d'utilisateur existe déjà
            $existingUser = $this->entityManager->getRepository(User::class)
                ->findOneBy(['userName' => $newUsername]);
            
            if ($existingUser && $existingUser !== $user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà pris.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->setUserName($newUsername);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Nom d\'utilisateur mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}