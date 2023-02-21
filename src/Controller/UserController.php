<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/valid/user', name: ('user'))]
class UserController extends AbstractController
{
    /*
     * voir tous les user
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: '_index')]
    public function home(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    /*
     * créer un user
     */
    #[Route('/create', name: '_create')]
    public function new(Request                     $request,
                        EntityManagerInterface      $em,
                        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //chiffrement du mot de passe avant enregistrement
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //assignation du role admin par un autre admin
            if ($this->isGranted('ROLE_ADMIN') && $form->get('isAdmin')->getData()) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/new.html.twig', ['form' => $form]);
    }

    /*
     * voir un user par id
     */
    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(
        int            $id,
        UserRepository $userRepo
    ): Response
    {   //Avec le id, retrouver l'utilisateur'
        $user = $userRepo->findOneBy(['id' => $id]);
        //Sinon redirection
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /*
     * modifier un user par id
     */
    #[Route('/edit/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function editProfil(Request                $request,
                               EntityManagerInterface $em,
                               UserRepository         $userRepo,
                               int                    $id
    ): Response
    {
        //Avec l'id, retrouver l'user en database
        $user = $userRepo->find($id);

        //si ni admin ni son propre compte retour case départ
        if (!$this->isGranted('ROLE_ADMIN') &&
            !$this->getUser()->getUserIdentifier() == $user->getEmail()) {
            $this->redirectToRoute('home_index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($user->getName()!=null && $user->getSurname()!=null){
                $user->setRoles(['ROLE_VALID']);
            }
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home_index');
        }
        return $this->render('user/editMyProfil.html.twig', ['form' => $form]);
    }

    /*
     * supprimer un compte avec l'id
     */
    #[Route('/delete/{id}', name: '_delete')]
    public function delete(int                    $id,
                           UserRepository         $userRepo,
                           EntityManagerInterface $em
    ): Response
    {
        //Avec l'id, retrouver l'user en database
        $user = $userRepo->find($id);
        //si ni admin ni son propre compte retour case départ
        if (!$this->isGranted('ROLE_ADMIN') &&
            !$this->getUser()->getUserIdentifier() == $user->getEmail()) {
            $this->redirectToRoute('home_index');
        }
        $em->remove($user);
        $em->flush();
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('user_index');
        }
        return $this->redirectToRoute('home_index');

    }
}
