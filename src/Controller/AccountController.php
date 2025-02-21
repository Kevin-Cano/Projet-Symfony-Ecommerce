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

final class AccountController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(Security $security, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        $user = $this->security->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/add-funds', name: 'app_account_add_funds', methods: ['POST'])]
    public function addFunds(Request $request): Response
    {
        $user = $this->security->getUser();
        $amount = $request->request->get('amount');

        if ($amount && is_numeric($amount)) {
            $user->setBalance($user->getBalance() + $amount);
            $this->entityManager->flush();
            return new Response('Funds added successfully!', 200);
        }

        return new Response('Invalid amount.', 400);
    }

    #[Route('/account/update-phone', name: 'app_account_update_phone', methods: ['POST'])]
    public function updatePhone(Request $request): Response
    {
        $user = $this->security->getUser();
        $phoneNumber = $request->request->get('phoneNumber');

        if ($phoneNumber && preg_match('/^\+?[0-9]{10,15}$/', $phoneNumber)) {
            $user->setPhoneNumber($phoneNumber);
            $this->entityManager->flush();
            return new Response('Phone number updated successfully!', 200);
        }

        return new Response('Invalid phone number.', 400);
    }

    #[Route('/account/update-password', name: 'app_account_update_password', methods: ['POST'])]
    public function updatePassword(Request $request): Response
    {
        $user = $this->security->getUser();
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        if ($newPassword && $confirmPassword && $newPassword === $confirmPassword) {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $this->entityManager->flush();
            return new Response('Password updated successfully!', 200);
        }

        return new Response('Passwords do not match or are empty.', 400);
    }

    #[Route('/account/delete-account', name: 'app_account_delete_account', methods: ['POST'])]
    public function deleteAccount(): Response
    {
        $user = $this->security->getUser();
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new Response('Account deleted successfully!', 200);
    }
}