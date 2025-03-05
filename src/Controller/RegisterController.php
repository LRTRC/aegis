<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Professional;
use App\Form\ParticipantType;
use App\Form\ProfessionalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function chooseType(): Response
    {
        return $this->render('register/choose.html.twig');
    }

    #[Route('/register/participant', name: 'app_register_participant')]
    public function registerParticipant(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte participant a été créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/participant.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/professional', name: 'app_register_professional')]
    public function registerProfessional(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        $professional = new Professional();
        $form = $this->createForm(ProfessionalType::class, $professional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professional->setPassword(
                $userPasswordHasher->hashPassword(
                    $professional,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($professional);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte professionnel a été créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/professional.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}