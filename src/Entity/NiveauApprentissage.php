<?php

namespace App\Entity;

use App\Repository\NiveauApprentissageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauApprentissageRepository::class)]
class NiveauApprentissage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Chien>
     */
    #[ORM\OneToMany(targetEntity: Chien::class, mappedBy: 'niveauxApprentissage')]
    private Collection $chiens;

    /**
     * @var Collection<int, Cours>
     */
    #[ORM\OneToMany(targetEntity: Cours::class, mappedBy: 'niveauxApprentissage')]
    private Collection $cours;

    public function __construct()
    {
        $this->chiens = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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
            $chien->setNiveauxApprentissage($this);
        }

        return $this;
    }

    public function removeChien(Chien $chien): static
    {
        if ($this->chiens->removeElement($chien)) {
            // set the owning side to null (unless already changed)
            if ($chien->getNiveauxApprentissage() === $this) {
                $chien->setNiveauxApprentissage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cours>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): static
    {
        if (!$this->cours->contains($cour)) {
            $this->cours->add($cour);
            $cour->setNiveauxApprentissage($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): static
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getNiveauxApprentissage() === $this) {
                $cour->setNiveauxApprentissage(null);
            }
        }

        return $this;
    }
}
