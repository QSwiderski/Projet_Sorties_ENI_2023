<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $credPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $cred = new Credentials();
        $form = $this->createForm(RegistrationFormType::class, $cred);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $cred->setPassword(
                $credPasswordHasher->hashPassword(
                    $cred,
                    $form->get('plainPassword')->getData()
                )
            );
            if ($form->get('isAdmin')->getData()) {
                $cred->setRoles(['ROLE_ADMIN']);
            }
            $entityManager->persist($cred);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home_admin');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
