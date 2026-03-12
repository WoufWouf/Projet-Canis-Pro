<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProprietaireRepository;

#[Route('/membre')]
final class MembreController extends AbstractController
{
    #[Route('', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'MembreController',
        ]);
    }

  /*   #[Route('/espace-personnel', name: 'espace_personnel')]
    public function espacePearsonnel(ProprietaireRepository $proprietaireRepository): Response
    {
        $proprietaires = $proprietaireRepository->findAll();
        
        if (empty($proprietaires)) {
            throw $this->createNotFoundException('Aucun propriétaire trouvé');
        }

        // Pour le test, on prend le premier propriétaire
        $proprietaire = $proprietaires[0];

        return $this->render('membre/espace_personnelle.html.twig', [
            'proprietaire' => $proprietaire,
            'chiens' => $proprietaire->getChiens(),
        ]);
    } */
}
