<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        Security $security,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $token = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $token))) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
            
            // Vérifier si l'email existe déjà
            $existingEmail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $form->get('email')->getData()
            ]);
            
            // Vérifier si le nom d'utilisateur existe déjà
            $existingUsername = $entityManager->getRepository(User::class)->findOneBy([
                'userName' => $form->get('userName')->getData()
            ]);

            if ($existingEmail) {
                $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée'));
            }

            if ($existingUsername) {
                $form->get('userName')->addError(new FormError('Ce nom d\'utilisateur est déjà pris'));
            }

            if ($form->isValid() && !$existingEmail && !$existingUsername) {
                $user->setRoles(['ROLE_USER']);
                $user->setBalance(0.0);
                $user->setProfilePicture('/images/default-profile.png');
                
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                return $security->login($user, LoginFormAuthenticator::class, 'main');
            }
        }

        return $this->render('register/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/check-duplicate', name: 'check_duplicate', methods: ['POST'])]
    public function checkDuplicate(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $field = $request->request->get('field');
        $value = $request->request->get('value');
        
        $repository = $entityManager->getRepository(User::class);
        
        $exists = match($field) {
            'email' => $repository->findOneBy(['email' => $value]) !== null,
            'userName' => $repository->findOneBy(['userName' => $value]) !== null,
            default => false
        };
        
        return new JsonResponse(['exists' => $exists]);
    }
}
