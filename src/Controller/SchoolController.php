<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\OrderBy;
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
            return $this->redirectToRoute('school_list');
        };

        return $this->render(
            'school/create.html.twig', ['schoolForm' => $schoolForm,
                'modify' => false,
                'title' => 'Créer']);
    }

    #[Route('/createwithname/', name: '_createwithname')]
    public function createSchoolWithName(
        EntityManagerInterface $em,
        Request                $request,
    ): Response
    {
        $name = $request->query->get('NomSchool','Merci de définir un nom');
        if ($name==""){
            $name='Merci de définir un nom';
        }

        $school = new School();
        $school->setName($name);
        $schoolForm = $this->createForm(SchoolType::class, $school);

        $schoolForm->handleRequest(($request));

        if ($schoolForm->isSubmitted() && $school->getName()!='Merci de définir un nom') {
            $em->persist($school);
            $em->flush();
            return $this->redirectToRoute('school_list');
        };

        return $this->render(
            'school/create.html.twig', ['schoolForm' => $schoolForm, 'modify'=>false]);
    }

    #[Route('/', name: '_list')]
    public function list(
        SchoolRepository $schoolRepository,
        Request $request
    ): Response
    {
        $schools = $request->query->get('schools');
        if ($schools==null){
            $schools = $schoolRepository->findAll();
        }
        return $this->render('school/list.html.twig',
            [
                "schools" => $schools
            ]);
    }

    #[Route('/research', name: '_listeresearch')]
    public function listeResearch(

        SchoolRepository $schoolRepository,
        EntityManagerInterface $em,
        Request $request,
    ): Response
    {
        $research = $request->query->get('Research');
        $schools = $schoolRepository->findBy(['name' => $research]);
        if ($schools==null){$schools='VIDE';}
        return $this->redirectToRoute('school_list',
            [
                "schools" => $schools,
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
    }
