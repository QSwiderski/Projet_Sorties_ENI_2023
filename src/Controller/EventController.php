<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\CredentialsRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
        CredentialsRepository $credRepo,
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        //trouver le pseudo loggué en session
        $pseudo = $this->getUser()->getUserIdentifier();
        //en trouver le responsable en DB
        $organizer = $credRepo->findOneBy(['pseudo'=>$pseudo]);

        $event = new Event();


        //on init les dates à afficher
        $timeSetter = new DateTime('now');
        $event->setDateStart(Clone($timeSetter)->setTime(17,0));
        $event->setDateFinish(Clone($timeSetter)->setTime(19,0));
        $event->setDateLimit(Clone($timeSetter)->setTime(12,0));

        // On cherche si on requete event_create en revenant de location_create
        $fromLoc = false;
        $keys = ['mem_name', 'mem_dateStart', 'mem_dateFinish', 'mem_dateLimit', 'mem_peopleMax', 'mem_description'];
        //on memorise chaque élément, en vérifiant au passage si le moindre d'entre eux est non null
        $values = new ArrayCollection();
        foreach ($keys as $key) {
            $value= $request->request->get($key);
            $values[$key]=$value;
            if ($value !=null && $value!== '') {
                $fromLoc = true;
            }
        }
        if($fromLoc){
            dd($values);
        }

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
