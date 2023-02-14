<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event', name: 'event')]
class EventController extends AbstractController
{
    #[Route('/{id}', name: '_showOne',requirements: ['id'=>'\d+'])]
    public function showOne(
        int $id,
        EventRepository $evRepo
    ): Response
    {
        $event = $evRepo->findOneBy(['id'=>$id]);
        return $this->render('event/index.html.twig', [
            'event'=>$event
        ]);
    }
}
