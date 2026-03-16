<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeanceRepository;

final class VisiteurController extends AbstractController
{
    #[Route('', name: 'app_visiteur')]
    public function index(): Response
    {
        return $this->render('visiteur/index.html.twig');
    }

    #[Route('/seance', name: 'app_seance')]
    public function seance(SeanceRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->findAll();
        
        return $this->render('visiteur/seance.html.twig', [
            'seances' => $seance,
        ]);
    }

    #[Route('/cours', name: 'app_listeCours')]
    public function cours(): Response
    {
        return $this->render('visiteur/listeCours.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
    }
    

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('visiteur/contact.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
    }
}
