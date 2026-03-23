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
use App\Form\InscriptionType;

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

    #[Route('/espace-personnel', name: 'espace_personnel')]
    public function espaceProprietaire(): Response
    {
        $user = $this->getUser();
        $proprietaire= $user->getProprietaire();
        return $this->render('membre/espace_personnel.html.twig', [
            'proprietaire' => $proprietaire,
        ]);
    }

    #[Route('/espace-chien', name: 'espace_chien')]
    public function espaceChien(): Response
    {
        $user = $this->getUser();
        $proprietaire= $user->getProprietaire();

        $chiens = $proprietaire->getChiens();


        return $this->render('membre/espace_chien.html.twig', [
            'proprietaire' => $proprietaire,
            'chiens' => $chiens,
        ]);
    }

    #[Route('/inscriptions/ajout/{seance}', name: 'app_reservation', methods: ['GET', 'POST'])]
    public function newInscription( Request $request, ChienRepository $chienRepository, EntityManagerInterface $entityManager, Seance $seance): Response 
    {
    $user = $this->getUser();
    $proprietaire = $user->getProprietaire();
    $chiens = $chienRepository->findBy(['proprietaire' => $proprietaire]);

    $inscription = new Inscription();
    $inscription->setSeance($seance); 

    $form = $this->createForm(InscriptionType::class, $inscription);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $idChien = $request->request->get('chien');
        $chien = $chienRepository->find($idChien);
        $inscription->setChien($chien); 
        $inscription->setNbChienInscrit(1);

        $entityManager->persist($inscription);
        $entityManager->flush();
        return $this->redirectToRoute('espace_personnel',  [],   Response::HTTP_SEE_OTHER);
    }

    return $this->render('inscription/new.html.twig', [
        'inscription' => $inscription,
        'form' => $form,
        'chiens' => $chiens,
    ]);
    }

    #[Route('/espace-chien/ajoutChien/{id}', name: 'membreAjoutChien')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $proprietaire = $user->getProprietaire();
        $chien = new Chien();
        $form = $this->createForm(ChienType::class, $chien);
        $chien->setProprietaire($proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chien->setProprietaire($proprietaire);
            $entityManager->persist($chien);
            $entityManager->flush();

            return $this->redirectToRoute('espace_chien',  Response::HTTP_SEE_OTHER);
        }

        return $this->render('chien/new.html.twig', [
            'chien' => $chien,
            'form' => $form,
            'proprietaire'=> $proprietaire
        ]);
    }

    

    #[Route('/mes-prochaines-seances', name: 'membre_mes_prochaines_seances')]
    public function mesSeances(SeanceRepository $seanceRepository): Response
    {
        // récupère le propriétaire de test (idem espaceProprietaire)
        $user = $this->getUser();
        $proprietaire = $user->getProprietaire();

        // toutes les séances disponibles
        $seances = $seanceRepository->findAll();

        return $this->render('membre/mes_seances.html.twig', [
            'proprietaire' => $proprietaire,
            'seances' => $seances[0]->getCours(), // pour l'exemple, on prend le cours de la première séance
        ]);
    }


#[Route('/membre/espace-personnel/modification/{id}', name: 'membre_proprietaire_modification', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function modifierUnProprietaires(Request $request, Proprietaire $proprietaire, Chien $chien, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    $proprietaire = $user->getProprietaire();
   

        $form = $this->createForm(ProprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($proprietaire);
        $entityManager->flush();

 $chiens = $proprietaire->getChiens();
             return $this->redirectToRoute('espace_personnel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('membre/modification_proprietaire.html.twig', [
            'proprietaire' => $proprietaire,
            'form' => $form,
        ]);
    }
    
    #[Route('/membre/espace-chien/modification/{id}', name: 'membre_chien_modification', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function modifierUnChien(
        int $id,
        ChienRepository $repository,
        EntityManagerInterface $entity,
        Request $request
    ): Response {

        // On récupère le chien
        $chien = $repository->find($id);

        // Si pas trouvé → erreur
        if (!$chien) {
            throw $this->createNotFoundException('Chien non trouvé');
        }

        // On sait qu'on est en modification
        $isModification = true;

        // Formulaire
        $form = $this->createForm(ChienType::class, $chien);
        $form->handleRequest($request);

        // Si validé → on modifie
        if ($form->isSubmitted() && $form->isValid()) {

            $entity->flush(); // pas besoin de persist

        $this->addFlash('success', 'Chien modifié avec succès');

            // Redirection vers l’espace chien du propriétaire
            return $this->redirectToRoute('espace_chien', [
                'id' => $chien->getProprietaire()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        // Affichage du formulaire
        return $this->render('membre/modification_chien.html.twig', [
            'form' => $form->createView(),
            'chien' => $chien,
            'isModification' => $isModification,
        ]);
    }

    #[Route('/espace-chien/seances/{id}', name: 'chien_inscrit_seance')]
    public function voirSeancesChien(ChienRepository $chienRepo, int $id): Response
    {
        $chien = $chienRepo->find($id);

        if (!$chien) {
            throw $this->createNotFoundException('Chien introuvable');
        }
        return $this->render('membre/chien_seances.html.twig', [
        'chien' => $chien,
        'inscriptions' => $chien->getInscriptions(),
        ]);
    }

    #[Route('/espace-chien/inscription/supression/{id}', name: 'membreSuppressionInscription')]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $chienId = $inscription->getChien()->getId();
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chien_inscrit_seance', [ 'id' => $chienId], Response::HTTP_SEE_OTHER);
    }

}