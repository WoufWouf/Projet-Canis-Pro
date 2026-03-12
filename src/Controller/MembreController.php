<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProprietaireRepository;
use App\Repository\ChienRepository;
use App\Repository\SeanceRepository;
use App\Entity\Inscription;
use App\Entity\Chien;
use App\Entity\Proprietaire;
use App\Entity\Seance;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/membre')]
final class MembreController extends AbstractController
{
<<<<<<< Updated upstream
    #[Route('', name: 'accueil')]
=======
    #[Route('/membre', name: 'accueil')]
>>>>>>> Stashed changes
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'MembreController',
        ]);
    }

    #[Route('/espace-personnel', name: 'espace_personnel')]
    public function espaceProprietaire(ProprietaireRepository $proprietaireRepository): Response
    {
        // Récupère l'ensemble des propriétaires. En pratique, on devrait
        // sélectionner celui de l'utilisateur connecté ou passer un ID.
        $proprietaires = $proprietaireRepository->findAll();
        
        if (empty($proprietaires)) {
            // La base de données est vide, on lève une exception 404
            throw $this->createNotFoundException('Aucun propriétaire trouvé');
        }

        // Pour le moment nous utilisons simplement le premier résultat comme
        // propriétaire de test.
        $proprietaire = $proprietaires[0];

        // On transmet le propriétaire à la vue qui affichera ses informations
        // et un aperçu de ses chiens.
        return $this->render('membre/espace_personnel.html.twig', [
            'proprietaire' => $proprietaire,
        ]);
    }

    #[Route('/espace-chien', name: 'espace_chien')]
    public function espaceChien(ProprietaireRepository $proprietaireRepository): Response
    {
        // Pour cet exemple, on récupère simplement un propriétaire existant.
        // En situation réelle, on utiliserait l'utilisateur connecté ou l'ID
        // passé en paramètre.
        $proprietaires = $proprietaireRepository->findAll();
        if (empty($proprietaires)) {
            throw $this->createNotFoundException('Aucun propriétaire trouvé');
        }
        $proprietaire = $proprietaires[0];

        // Grâce à la relation OneToMany définie dans l'entité,
        // on peut directement obtenir la collection de chiens liés.
        $chiens = $proprietaire->getChiens();

        // on transmet à la vue à la fois le propriétaire et ses chiens
        return $this->render('membre/espace_chien.html.twig', [
            'proprietaire' => $proprietaire,
            'chiens' => $chiens,
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
            'seances' => $seances,
        ]);
    }

    #[Route('/inscription/{seance}/{chien}', name: 'membre_inscrire', methods: ['GET'])]
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

        return $this->render('membre/inscription_chien.html.twig', [
            'inscription' => $inscription,
        ]);
    }

}
