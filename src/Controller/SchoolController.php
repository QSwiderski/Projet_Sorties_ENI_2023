<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/school', name: 'school')]
class SchoolController extends AbstractController
{
    /*
     * voir toutes les écoles
     * (pas de vue une école à la fois)
     */
    #[Route('/', name: '_index')]
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

    /*
     * créer une école
     */
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
            return $this->redirectToRoute('school_index');
        };

        return $this->render(
            'school/create.html.twig', ['schoolForm' => $schoolForm,
            'modify' => false,
            'title' => 'Créer']);
    }

    /*
     * Modifier une école par id
     */
    #[Route("/edit/{id}", name: '_modify',requirements: ['id'=>'\d+'])]
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
            return $this->redirectToRoute('school_index', ["id" => $school->getId()]);
        }
        $this->addFlash('succes', 'Votre modification est bien enregistrée');
        return $this->render('school/create.html.twig', [
            'schoolForm' => $schoolForm,
            'modify' => true,
            'title' => 'Modification'
        ]);

    }

    /*
     * supprimer une école par id
     */
    #[Route("/delete/{id}", name: '_delete', requirements: ["id" => '\d+'])]
    public function delete(int                    $id,
                           EntityManagerInterface $em,
                           SchoolRepository       $schoolRepository)
    {
        $school = $schoolRepository->find($id);
        $em->remove($school);
        $em->flush();
        return $this->redirectToRoute(
            'school_index');
    }
}
