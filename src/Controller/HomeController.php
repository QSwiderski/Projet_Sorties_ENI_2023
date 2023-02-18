<?php

namespace App\Controller;

use App\Repository\CredentialsRepository;
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
    public function index(Request $request,EntityManagerInterface $em, CredentialsRepository $credentialsRepository): Response
    {
        //récupération du pseudo de la session
        $pseudo= $this->getUser()->getUserIdentifier();

        if ($pseudo == null){
            return $this->redirectToRoute('event_showAll');
        }


        //Avec le pseudo, retrouver l'objet Credentials
        $cred= $credentialsRepository->findOneBy(['pseudo'=>$pseudo]);
        //tentative de récupération du User si il existe
        $user = $cred->getUser();

          if ($user == null) {
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
