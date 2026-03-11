<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $typeEntrainement = null;

    #[ORM\Column(length: 25)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column]
    private ?bool $esCollectif = null;

    #[ORM\Column]
    private ?int $nbChienMax = null;

    #[ORM\Column]
    private ?int $duree = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeEntrainement(): ?string
    {
        return $this->typeEntrainement;
    }

    public function setTypeEntrainement(string $typeEntrainement): static
    {
        $this->typeEntrainement = $typeEntrainement;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function isEsCollectif(): ?bool
    {
        return $this->esCollectif;
    }

    public function setEsCollectif(bool $esCollectif): static
    {
        $this->esCollectif = $esCollectif;

        return $this;
    }

    public function getNbChienMax(): ?int
    {
        return $this->nbChienMax;
    }

    public function setNbChienMax(int $nbChienMax): static
    {
        $this->nbChienMax = $nbChienMax;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }
}
