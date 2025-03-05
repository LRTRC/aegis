<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Professional;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(
        AuthenticationUtils $authenticationUtils, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Récupérer l'utilisateur Participant
        $participant = $entityManager->getRepository(Participant::class)->findOneByEmail($lastUsername);
        if ($participant && $userPasswordHasher->isPasswordValid($participant, $authenticationUtils->getLastUsername())) {
            return $this->redirectToRoute('app_home');
        }
    
        // Récupérer l'utilisateur Professional
        $professional = $entityManager->getRepository(Professional::class)->findOneByEmail($lastUsername);
        if ($professional && $userPasswordHasher->isPasswordValid($professional, $authenticationUtils->getLastUsername())) {
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('login/login.html.twig', [
            'controller_name' => 'LoginController',
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}