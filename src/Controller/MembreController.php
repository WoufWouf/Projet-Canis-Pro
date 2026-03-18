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
use App\Form\ProprietaireType;
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

#[Route('/membre/espace-personnel/modification/{id}', name: 'membre_proprietaire_modification', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function modifierUnProprietaire(
    ?int $id, 
    ProprietaireRepository $repository, 
    EntityManagerInterface $entity, 
    Request $request // <-- IL FAUT AJOUTER CECI ICI
): Response {

    $proprietaire = $repository->findOneBy(['id' => $id]);
    $isModification = $proprietaire !== null;

    if (!$proprietaire) {
        $proprietaire = new Proprietaire();
    }

    $form = $this->createForm(ProprietaireType::class, $proprietaire);
    $form->handleRequest($request); // Maintenant, $request est reconnu !

    // On ne persiste/flush QUE si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        $entity->persist($proprietaire);
        $entity->flush();

        $this->addFlash('success', $isModification ? 'Propriétaire modifié' : 'Propriétaire ajouté');
        
        // Redirection pour éviter de renvoyer le formulaire en rafraîchissant
        return $this->redirectToRoute('espace_personnel', ['id' => $proprietaire->getId()]);
    }

    return $this->render('membre/modification_proprietaire.html.twig', [
        'form' => $form->createView(), // <-- BIEN PASSER LE FORMULAIRE ICI
        'proprietaire' => $proprietaire,
        'isModification' => $isModification,
    ]);
}

#[Route('/membre/espace-chien/modification/{id}', name: 'membre_chien_modification', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function modifierUnChien(
    int $id,
    ChienRepository $repository,
    EntityManagerInterface $entity,
    Request $request
): Response {

    // 🔍 On récupère le chien
    $chien = $repository->find($id);

    // ❌ Si pas trouvé → erreur
    if (!$chien) {
        throw $this->createNotFoundException('Chien non trouvé');
    }

    // ✅ On sait qu'on est en modification
    $isModification = true;

    // 📝 Formulaire
    $form = $this->createForm(ChienType::class, $chien);
    $form->handleRequest($request);

    // ✅ Si validé → on modifie
    if ($form->isSubmitted() && $form->isValid()) {

        $entity->flush(); // pas besoin de persist

        $this->addFlash('success', 'Chien modifié avec succès 🐶');

        // 🔁 Redirection vers l’espace chien du propriétaire
        return $this->redirectToRoute('espace_chien', [
            'id' => $chien->getProprietaire()->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    // 🎨 Affichage du formulaire
    return $this->render('membre/modification_chien.html.twig', [
        'form' => $form->createView(),
        'chien' => $chien,
        'isModification' => $isModification,
    ]);
}

}