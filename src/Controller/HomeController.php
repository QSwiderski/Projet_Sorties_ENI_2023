<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'home')]
class HomeController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('event_showAll');
    }

    #[Route('/administrator', name: '_admin')]
    public function admin(): Response
    {
        return $this->render('home/adminpanel.html.twig');
    }
}
