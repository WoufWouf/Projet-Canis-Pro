<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_Chien_Inscrit = null;

    // ✅ UNE inscription = UN chien
    #[ORM\ManyToOne(targetEntity: Chien::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chien $chien = null;

    // ✅ UNE inscription = UNE séance
    #[ORM\ManyToOne(targetEntity: Seance::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seance $seance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbChienInscrit(): ?int
    {
        return $this->nb_Chien_Inscrit;
    }

    public function setNbChienInscrit(?int $nb_Chien_Inscrit): static
    {
        $this->nb_Chien_Inscrit = $nb_Chien_Inscrit;
        return $this;
    }

    // ✅ GETTER / SETTER CHIEN
    public function getChien(): ?Chien
    {
        return $this->chien;
    }

    public function setChien(?Chien $chien): static
    {
        $this->chien = $chien;
        return $this;
    }

    // ✅ GETTER / SETTER SEANCE
    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;
        return $this;
    }
}