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
}
