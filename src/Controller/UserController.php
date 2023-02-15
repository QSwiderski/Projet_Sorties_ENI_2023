<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index')]
    public function home(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', ['users' => $users]);
    }


    #[Route('/new', name: 'app_user_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface
    $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        //$credentials = new Credentials();
        //$credentials->setUser($user);

        //$formReg = $this->createForm(RegistrationFormType::class, $credentials);

        $form->handleRequest($request);
        //$formReg->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $credentials =  $user->getCredentials();
            $credentials->setPassword(
                    $userPasswordHasher->hashPassword(
                        $credentials,
                        $form->get('credentials')->get('plainPassword')->getData()
                    )
                );

            $em->persist($user);
            //$em->persist($credentials);
            $em->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', ['form'=>$form]);
    }


    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_user_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', compact('form'));
    }


    #[Route('/delete/{id}', name: 'app_user_delete')]
    public function delete(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $userRemoved = $userRepository->findOneBy(['id' => $id]);
        $em->remove($userRemoved);
        $em->flush();

        return $this->redirectToRoute('app_user_index');

    }
}
