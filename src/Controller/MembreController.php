<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProprietaireRepository;
use App\Repository\ChienRepository;
use App\Repository\SeanceRepository;
use App\Entity\Inscription;
use App\Entity\Chien;
use App\Form\ChienType;
use App\Entity\Proprietaire;
use App\Entity\Seance;
use App\Entity\Cours;

#[Route('/membre')]
final class MembreController extends AbstractController
{

    #[Route('/membre', name: 'accueil')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'MembreController',
        ]);
    }

    #[Route('/espace-personnel/{id}', name: 'espace_personnel')]
    public function espaceProprietaire(Proprietaire $proprietaire): Response
    {
        return $this->render('membre/espace_personnel.html.twig', [
            'proprietaire' => $proprietaire,
        ]);
    }

    #[Route('/espace-chien/{id}', name: 'espace_chien')]
    public function espaceChien(Proprietaire $proprietaire): Response
    {
        // Grâce à la relation OneToMany définie dans l'entité,
        // on peut directement obtenir la collection de chiens liés.
        $chiens = $proprietaire->getChiens();

        // on transmet à la vue à la fois le propriétaire et ses chiens
        return $this->render('membre/espace_chien.html.twig', [
            'proprietaire' => $proprietaire,
            'chiens' => $chiens,
        ]);
    }

    #[Route('/espace-chien/ajoutChien/{id}', name: 'membreAjoutChien')]
    public function new(Request $request, EntityManagerInterface $entityManager, Proprietaire $proprietaire): Response
    {
        $chien = new Chien();
        $form = $this->createForm(ChienType::class, $chien);
        $chien->setProprietaire($proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chien->setProprietaire($proprietaire);
            $entityManager->persist($chien);
            $entityManager->flush();

            return $this->redirectToRoute('espace_chien', [
                        'id' => $proprietaire->getId()
                    ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chien/new.html.twig', [
            'chien' => $chien,
            'form' => $form,
            'proprietaire'=> $proprietaire
        ]);
    }

    #[Route('/mes-prochaines-seances', name: 'membre_mes_prochaines_seances')]
    public function mesSeances(ProprietaireRepository $proprietaireRepository, SeanceRepository $seanceRepository): Response
    {
        // récupère le propriétaire de test (idem espaceProprietaire)
        $proprietaires = $proprietaireRepository->findAll();
        if (empty($proprietaires)) {
            throw $this->createNotFoundException('Aucun propriétaire trouvé');
        }
        $proprietaire = $proprietaires[0];

        // toutes les séances disponibles
        $seances = $seanceRepository->findAll();

        return $this->render('membre/mes_seances.html.twig', [
            'proprietaire' => $proprietaire,
            'seances' => $seances[0]->getCours(), // pour l'exemple, on prend le cours de la première séance
        ]);
    }

    #[Route('/inscription/{seance}/{chien}', name: 'membre_inscrit', methods: ['GET'])]
    public function inscriptionChien(SeanceRepository $seanceRepo, ChienRepository $chienRepo, EntityManagerInterface $entityManager, $seance, $chien): Response
    {
        // cette route est volontairement simple : elle crée une inscription
        // reliant un chien et une séance puis affiche un accusé de réception.
        $seanceObj = $seanceRepo->find($seance);
        $chienObj = $chienRepo->find($chien);

        if (!$seanceObj || !$chienObj) {
            throw $this->createNotFoundException('Séance ou chien introuvable ou chien déja inscrit à cette séance');
        }

        $inscription = new Inscription();
        $inscription->addSeance($seanceObj);
        $inscription->addChien($chienObj);

        $entityManager->persist($inscription);
        $entityManager->flush();

        // récupérer le propriétaire du chien inscrit pour afficher la liste de
        // tous ses chiens (niveau inclus)
        $proprietaire = $chienObj->getProprietaire();

        return $this->render('membre/inscription_chien.html.twig', [
            'inscription' => $inscription,
            'proprietaire' => $proprietaire,
        ]);
    }

}
