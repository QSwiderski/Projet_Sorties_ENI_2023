<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school', name: 'school')]
class SchoolController extends AbstractController

{
    #[Route('/create', name: '_create')]
    public function createSchool(
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $school = new School();

        $schoolForm = $this->createForm(SchoolType::class, $school);

        $schoolForm->handleRequest(($request));

        if ($schoolForm->isSubmitted()) {
            $em->persist($school);
            $em->flush();
            return $this->redirectToRoute('school_create');
        };

        return $this->render(
            'school/create.html.twig', ['schoolForm' => $schoolForm,
                'modify' => false,
                'title' => 'Créer']);
    }

    #[Route('/', name: '_list')]
    public function list(
        SchoolRepository $schoolRepository,
    ): Response
    {
        $schools = $schoolRepository->findAll();
        return $this->render('school/list.html.twig',
            [
                "schools" => $schools
            ]);
    }

    #[Route("/delete/{id}", name: '_delete', requirements: ["id" => '\d+'])]
    public function delete(Request                $request,
                           int                    $id,
                           EntityManagerInterface $em,
                           SchoolRepository       $schoolRepository)
    {

        $school = $schoolRepository->find($id);
        $em->remove($school);
        $em->flush();

        return $this->redirectToRoute(
            'school_list');
    }

    #[Route("/modify/{id}", name: '_modify')]
    public function modify(
        int                    $id,
        SchoolRepository       $schoolRepository,
        EntityManagerInterface $em,
        Request                $request
    ): Response
    {
        $school = $schoolRepository->findOneBy(['id' => $id]);
        $schoolForm = $this->createForm(SchoolType::class, $school);
        $schoolForm->handleRequest($request);
        if ($schoolForm->isSubmitted() && $schoolForm->isValid()) {
            $em->persist($school);
            $em->flush();
            return $this->redirectToRoute('school_list', ["id" => $school->getId()]);
        }
        $this->addFlash('succes', 'Votre modification est bien enregistrée');
        return $this->render('school/create.html.twig', [
            'schoolForm' => $schoolForm,
            'modify' => true,
            'title' => 'Modification'
        ]);

    }


    /*#[Route('/{recherche}', name: '_rechercher')]
    public function rechercher(
        string           $name,
        SchoolRepository $schoolRepository,
        Request          $request
    ): Response
    {

        $schools = $schoolRepository->findBy(["name" => $name]);
        return $this->render('school/list.html.twig', compact('schools'));
    }*/


}
