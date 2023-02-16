<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'home')]
class HomeController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(Request $request,EntityManagerInterface $em): Response
    {
          if ($request->getUser() == null) {
              return $this->redirectToRoute('app_user_edition');
          }
          else {
              return $this->redirectToRoute('event_showAll');
          }



    }

    #[Route('/administrator', name: '_admin')]
    public function admin(): Response
    {
        return $this->render('home/adminpanel.html.twig');
    }
}
