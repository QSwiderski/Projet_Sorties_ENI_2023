<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event', name: 'event')]
class EventController extends AbstractController
{


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
        UserRepository $userRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        //BOUCHON SA MERE

        $organizer = $userRepo->find(1);
        $event = new Event();
        $form = $this->createForm(EventType::class,$event);
                $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganizer($organizer);
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('event_showOne',["id"=>$event->getId()]);
        }
        $this->addFlash('success','Votre evenement est bien enregistré');
        return $this->render('event/create.html.twig', [
            'form' => $form
        ]
        );
    }
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
}
