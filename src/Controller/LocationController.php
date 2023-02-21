<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Location;
use App\Form\LocationType;
use App\Memory;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/valid/location', name: 'location')]
class LocationController extends AbstractController

{
    /*
     * Afficher tous les lieux
     */
    #[Route('/', name: '_index')]
    public function index(
        LocationRepository $locRepo
    ): Response
    {
        $locations = $locRepo->findAll();
        return $this->render('location/index.html.twig', [
            'locations' => $locations
        ]);
    }

    /*
     * Afficher un lieu par son id
     */
    #[Route('/{id}', name: '_show', requirements: ['id' => '\d+'])]
    public function showOne(
        int                $id,
        LocationRepository $locRepo
    ): Response
    {
        $location = $locRepo->findOneBy(['id' => $id]);
        if ($location == null) {
            return $this->redirectToRoute('location_index');
        }
        return $this->render('location/unique.html.twig', [
            'location' => $location
        ]);
    }

    /*
     * Créer un nouveau lieu
     */
    #[Route('/create', name: '_create')]
    public function create(
        EntityManagerInterface $em,
        Request                $request,
        Memory                 $mem
    ): Response
    {
        $location = new Location();
        //arrivant de event_create on retient les infos
        $mem->write($this->getUser()->getUserIdentifier(), $request->request);
        //on gère le formulaire normal de lieu
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();
            $this->addFlash('success', 'Bien enregistré');

            //TODO cherche si on requete location_create depuis une création d'event
            if ($this->isGranted('ROLE_ADMIN')) {
                //on retourne à l'affichage index (sur adminpanel)
                return $this->redirectToRoute('location_show', ["id" => $location->getId()]);
            }
            return $this->redirectToRoute('event_create');
        }

        return $this->render('location/create.html.twig', [
                'form' => $form,
                'edit' => false
            ]
        );
    }

    /*
     * Modifier un lieu par son id
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{id}', name: '_edit')]
    public function edit(
        int                    $id,
        EntityManagerInterface $em,
        Request                $request,
        LocationRepository     $locRepo
    ): Response
    {
        $location = $locRepo->findOneBy(['id' => $id]);
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();
            $this->addFlash('success', 'Votre modification est bien enregistrée');
            return $this->redirectToRoute('location_show', ["id" => $location->getId()]);
        }
        return $this->render('location/create.html.twig', [
            'form' => $form,
            'edit' => true
        ]);


    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete(
        int                    $id,
        locationRepository     $locRepo,
        EntityManagerInterface $em
    ): Response
    {
        $location = $locRepo->findOneBy(['id' => $id]);
        $em->remove($location);
        $em->flush();
        return $this->redirectToRoute('location_index');
    }
}
