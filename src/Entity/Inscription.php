<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\ManyToMany(targetEntity: Seance::class, inversedBy: 'inscriptions')]
    private Collection $seances;

    /**
     * @var Collection<int, Chien>
     */
    #[ORM\ManyToMany(targetEntity: Chien::class, inversedBy: 'inscriptions')]
    private Collection $chiens;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->chiens = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): static
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): static
    {
        $this->seances->removeElement($seance);

        return $this;
    }

    /**
     * @return Collection<int, Chien>
     */
    public function getChiens(): Collection
    {
        return $this->chiens;
    }

    public function addChien(Chien $chien): static
    {
        if (!$this->chiens->contains($chien)) {
            $this->chiens->add($chien);
        }

        return $this;
    }

    public function removeChien(Chien $chien): static
    {
        $this->chiens->removeElement($chien);

        return $this;
    }
}
