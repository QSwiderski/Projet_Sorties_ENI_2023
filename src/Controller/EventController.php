<?php

namespace App\Controller;

use App\Entity\Event;
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
        UserRepository         $userRepo,
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $organizer = $userRepo->find(14);//BOUCHON SA MERE

        $event = new Event();
        //Initialisation 'par defaut' des variables du nouvel event
        $timeSetter = new DateTime('now');
        $timeSetter->setTime(0,0,0);
        $event->setDateStart($timeSetter);
        $event->setDateFinish($timeSetter);
        $event->setDateLimit($timeSetter);
        //fin initialisation

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form->get('event_location')->getData()!=null) {
            $event->setOrganizer($organizer);
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('event_showOne', ["id" => $event->getId()]);
        }
        $this->addFlash('success', 'Votre evenement est bien enregistré');
        return $this->render('event/create.html.twig', [
                'form' => $form,
                'edit' => false
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

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(
        int                    $id,
        EntityManagerInterface $em,
        Request                $request,
        EventRepository        $evRepo
    ): Response
    {
        $event = $evRepo->findOneBy(['id' => $id]);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('event_showOne', ["id" => $event->getId()]);
        }
        $this->addFlash('success', 'Votre modification est bien enregistrée');
        return $this->render('event/create.html.twig', [
            'form' => $form,
            'edit' =>true
        ]);
    }

    #[Route('/remove/{id}', name: '_remove')]
    public function remove(
        int             $id,
        EventRepository $evRepo,
        EntityManagerInterface $em
    ): Response
    {
        $event = $evRepo->findOneBy(['id' => $id]);
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('event_showAll');

    }
}
