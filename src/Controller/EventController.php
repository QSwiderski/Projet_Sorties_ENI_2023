<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event', name: 'event')]
class EventController extends AbstractController
{
    #[Route('/', name: '_showAll')]
    public function showAll(
        EventRepository $evRepo
    ): Response
    {
        $events = $evRepo->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/{id}', name: '_showOne', requirements: ['id' => '\d+'])]
    public function showOne(
        int             $id,
        EventRepository $evRepo
    ): Response
    {
        $event = $evRepo->findOneBy(['id' => $id]);
        return $this->render('event/unique.html.twig', [
            'event' => $event
        ]);
    }


    #[Route('/new', name: '_create')]
    public function create(
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class,$event);
        /*
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event->(true);
            $event->(new \dateTime());
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('wish_wishes',["id"=>$wish->getId()]);
        }
        $this->addFlash('great_success','Panier ! Un souhaite de plus dans le Seau');
        */
        return $this->render('event/create.html.twig', [
            'form' => $form
        ]
        );
    }
}
