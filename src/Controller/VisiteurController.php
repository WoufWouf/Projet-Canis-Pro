<?php

namespace App\Controller;

use App\Entity\Proprietaire;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SeanceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class VisiteurController extends AbstractController
{
    #[Route('', name: 'app_visiteur')]
    public function index(): Response
    {
        return $this->render('visiteur/index.html.twig');
    }

    #[Route('/cours', name: 'app_listeCours')]
    public function cours(): Response
    {
        return $this->render('visiteur/listeCours.html.twig', [
            'controller_name' => 'VisiteurController',
        ]);
    }

    #[Route('/seance', name: 'app_seance')]
    public function seance(SeanceRepository $seanceRepository): Response
    {
        $seance = $seanceRepository->findAll();
        
        return $this->render('visiteur/seance.html.twig', [
            'seances' => $seance,
        ]);
    }

    #[Route('/information', name: 'app_information')]
    public function information(): Response
    {
        return $this->render('visiteur/information.html.twig', [
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

    #[Route('/inscription', name: 'inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
 
        if ($request->isMethod('POST')) {
 
            // ── Récupération des champs ──────────────────────────────
            $login           = $request->request->get('login');
            $email           = $request->request->get('email')    ?: null;
            $password        = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            $nom             = $request->request->get('nom');
            $prenom          = $request->request->get('prenom');
            $dateNaissance   = $request->request->get('date_naissance');
            $tel             = $request->request->get('tel')       ?: null;
            $adresse         = $request->request->get('adresse');
            $codePostal      = (int) $request->request->get('code_postal');
            $ville           = $request->request->get('ville');
 
            // ── Validation CSRF ──────────────────────────────────────
            if (!$this->isCsrfTokenValid('register', $request->request->get('_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('inscription');
            }
 
            // ── Validations basiques ─────────────────────────────────
            if ($password !== $passwordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('inscription');
            }
 
            if (strlen($password) < 8) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                return $this->redirectToRoute('inscription');
            }
 
            // ── Création de l'Utilisateur ────────────────────────────
            $user = new Utilisateur();
            $user->setLogin($login)
                 ->setRoles(['ROLE_USER'])
                 ->setPassword($passwordHasher->hashPassword($user, $password));
 
            // ── Création du Proprietaire ─────────────────────────────
            $proprietaire = new Proprietaire();
            $proprietaire->setNom($nom)
                         ->setPrenom($prenom)
                         ->setEmail($email)
                         ->setTel($tel)
                         ->setDateNaissance($dateNaissance)
                         ->setAdresse($adresse)
                         ->setCodePostal($codePostal)
                         ->setVille($ville)
                         ->setUser($user);  // ← lie les deux entités (côté Proprietaire)
 
            // ── Persistance ──────────────────────────────────────────
            // Grâce au cascade: ['persist', 'remove'] sur Utilisateur,
            // persister Proprietaire suffit — mais on persiste les deux par clarté
            $em->persist($user);
            $em->persist($proprietaire);
            $em->flush();
 
            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('espace_personnel'); // ← adapte la route cible
        }
 
        // ── GET : affichage du formulaire ────────────────────────────
        return $this->render('visiteur/inscription.html.twig');
    }
}
