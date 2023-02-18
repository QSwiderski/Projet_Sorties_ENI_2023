<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\AdminType;
use App\Form\UserType;
use App\Repository\CredentialsRepository;
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


    #[Route('/editMyProfile', name: 'app_user_edition')]
    public function editProfil (Request $request,
                                EntityManagerInterface $em,
                                CredentialsRepository $credentialsRepository,
                                UserPasswordHasherInterface
                                $userPasswordHasher): Response
    {

        //récupération du pseudo de la session
        $pseudo= $this->getUser()->getUserIdentifier();
        //Avec le pseudo, retrouver l'objet Credentials
        $cred= $credentialsRepository->findOneBy(['pseudo'=>$pseudo]);
        //tentative de récupération du User si il existe
        $user = $cred->getUser();
        //Sinon création du User
        if ($cred->getUser() == null){
            $user = new User();
        }
        //Faire la OneToOne entre l'objet Credentials et le nouvel objet User
        $user->setCredentials($cred);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            //$em->persist($cred);
            $em->flush();
            return $this->redirectToRoute('home_index');
        }
        return $this->render('user/editMyProfil.html.twig',['form'=> $form]);
    }

    #[Route('/new', name: 'app_user_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface
    $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(AdminType::class, $user);
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
        $form = $this->createForm(AdminType::class, $user);
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
