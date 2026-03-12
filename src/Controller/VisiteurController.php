<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeanceRepository;

final class VisiteurController extends AbstractController
{
    #[Route('/visiteur', name: 'app_visiteur')]
    public function index(): Response
    {
        return $this->render('visiteur/index.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
    }

        #[Route('/visiteur/seance', name: 'app_visiteur_seance')]
    public function seance(SeanceRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->findAll();

        return $this->render('visiteur/seance.html.twig', [
            'seances' => $seance,
        ]);
    }
}
