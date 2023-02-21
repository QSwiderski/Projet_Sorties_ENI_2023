<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'home')]
class HomeController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(
        UserRepository $userRepo
    ): Response
    {
        //si aucun user redirect 'accueil'
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('event_index');
        }
        if (!$this->isGranted('ROLE_USER_VALID')) {
            return $this->redirectToRoute('user_edit');
        }
        return $this->redirectToRoute('event_index');
        
    }

    #[Route('/administrator', name: '_admin')]
    public function admin(): Response
    {
        return $this->render('home/adminpanel.html.twig');
    }
}
