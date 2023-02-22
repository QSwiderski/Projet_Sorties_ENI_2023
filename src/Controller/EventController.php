<?php

namespace App\Controller;

use App\Form\EventDeleteType;
use App\Form\EventType;
use App\Memory;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\ToolKitBQP;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event', name: 'event')]
class EventController extends AbstractController
{
    /*
     * voir toutes les sorties
     * avec filtre
     */
    #[Route('/', name: '_index')]
    public function showAll(
        EntityManagerInterface $em,
        EventRepository        $evRepo,
        ToolKitBQP             $tool
    ): Response
    {
        $tool->autoArchive($em, $evRepo);
        $events = $evRepo->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    /*
    * publier une sortie
    */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/publish/{id}', name: '_publish', requirements: ['id' => '\d+'])]
    public function publish(
        int                    $id,
        EventRepository        $evRepo,
        EntityManagerInterface $em
    ): Response
    {
        //retrouver l'event en database
        $event = $evRepo->find($id);
        //si ni admin ni son propre event retour case départ
        if ($event == null || !$this->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getUserIdentifier() !== $event->getOrganizer()->getEmail()) {
            return $this->redirectToRoute('home_index');
        }
        $event->setPublished(true);
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('home_index');
    }


    /*
     * voir une sortie par id
     */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/{id}', name: '_show', requirements: ['id' => '\d+'])]
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

    /*
     * Créer une sortie
     */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/new', name: '_create')]
    public function create(
        UserRepository         $userRepo,
        EntityManagerInterface $em,
        Request                $request,
        Memory                 $mem
    ): Response
    {
        //trouver le mail loggué en session
        $mail = $this->getUser()->getUserIdentifier();
        $organizer = $userRepo->findOneBy(['email' => $mail]);
        //on récupere un new event() qui peux avoir des infos déjà renseignées
        $event = $mem->createAnEvent($mail);
        //on gère le formulaire normal de Event
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganizer($organizer);
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('event_show', ["id" => $event->getId()]);
        }
        $this->addFlash('success', 'Votre evenement est bien enregistré');
        return $this->render('event/create.html.twig', [
                'form' => $form,
                'edit' => false
            ]
        );
    }

    /*
     * modifier une sortie par id
     */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/edit/{id}', name: '_edit')]
    public function edit(
        int                    $id,
        EntityManagerInterface $em,
        Request                $request,
        EventRepository        $evRepo
    ): Response
    {

        //retrouver l'event en database
        $event = $evRepo->find($id);
        //si ni admin ni son propre event retour case départ
        if ($event == null || !$this->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getUserIdentifier() !== $event->getOrganizer()->getEmail()) {
            return $this->redirectToRoute('home_index');
        }
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('event_show', ["id" => $event->getId()]);
        }
        $this->addFlash('success', 'Votre modification est bien enregistrée');
        return $this->render('event/create.html.twig', [
            'form' => $form,
            'edit' => true
        ]);
    }

    /*
     * supprimer un évenement
     */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/remove/{id}', name: '_remove')]
    public function remove(
        int                    $id,
        EventRepository        $evRepo,
        EntityManagerInterface $em,
        Request                $request,
        ToolKitBQP             $tool
    ): Response
    {
        //retrouver l'event en database
        $event = $evRepo->find($id);
        //si ni admin ni son propre event retour case départ
        if (!$this->isGranted('ROLE_ADMIN') &&
            !$this->getUser()->getUserIdentifier() == $event->getOrganizer()->getEmail()) {
            $this->redirectToRoute('home_index');
        }
        //on fait remplir la raison de suppression
        $form = $this->createForm(EventDeleteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //ajout de la raison de suppression
            $event->setCancelReason($form->get('cancel_reason')->getData());

            //TODO envoyer un mail aux participants  function cancel($event)
            //archivage
            $tool->archive($event,$this->isGranted('ROLE_ADMIN')?'ADMIN':'USER', $em);

            return $this->redirectToRoute('event_index');
        }
        return $this->render('event/delete.html.twig', [
            'event' => $event,
            'delete_form' => $form,
            'edit' => true
        ]);
    }

    /*
     * ajouter un utilisateur à un event
     */
    #[IsGranted('ROLE_USER_VALID')]
    #[Route('/apply/{id}', name: '_apply')]
    public function apply(
        int                    $id,
        EventRepository        $evRepo,
        UserRepository         $userRepo,
        EntityManagerInterface $em
    ): Response
    {
        //retrouver l'event en database
        $event = $evRepo->find($id);
        $user = $userRepo->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        //ajout/retrait de l'user dans l'event
        $event = $event->apply($user);
        $em->persist($event);
        $em->flush();
        $this->addFlash('success', 'Votre modification est bien enregistrée');

        return $this->redirectToRoute('home_index');
    }
}
