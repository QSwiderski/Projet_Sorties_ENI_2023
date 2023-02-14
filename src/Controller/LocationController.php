<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/location', name: 'location')]
class LocationController extends AbstractController

{
    #[Route('/', name: '_showAll')]
    public function index(
        LocationRepository $locRepo
    ): Response
    {
        $locations = $locRepo->findAll();
        return $this->render('location/index.html.twig', [
            'locations' => $locations
        ]);
    }

    #[Route('/{id}', name: '_showOne', requirements: ['id' => '\d+'])]
    public function showOne(
        int                $id,
        LocationRepository $locRepo
    ): Response
    {
        $location = $locRepo->findOneBy(['id' => $id]);
        return $this->render('location/unique.html.twig', [
            'location' => $location
        ]);
    }

    #[Route('/new', name: '_create')]
    public function create(
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
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
        return $this->render('wish/create.html.twig', [
                'form' => $form
            ]
        );
    }
}
