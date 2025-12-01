<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $telephone = null;

    #[ORM\Column(length: 50)]
    private ?string $sexe = null;

    #[ORM\Column(length: 50)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'patients')]
    private ?Localite $Localite = null;
    /**
     * @var Collection<int, Sejour>
     */
    #[ORM\OneToMany(targetEntity: Sejour::class, mappedBy: 'patient')]
    private Collection $Sejour;

    public function __construct()
    {
        $this->Sejour = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getLocalite(): ?Localite
    {
        return $this->Localite;
    }

    public function setLocalite(?Localite $Localite): static
    {
        $this->Localite = $Localite;

        return $this;
    }

    /**
     * @return Collection<int, Sejour>
     */
    public function getSejour(): Collection
    {
        return $this->Sejour;
    }

    public function addSejour(Sejour $sejour): static
    {
        if (!$this->Sejour->contains($sejour)) {
            $this->Sejour->add($sejour);
            $sejour->setPatient($this);
        }

        return $this;
    }

    public function removeSejour(Sejour $sejour): static
    {
        if ($this->Sejour->removeElement($sejour)) {
            // set the owning side to null (unless already changed)
            if ($sejour->getPatient() === $this) {
                $sejour->setPatient(null);
            }
        }

        return $this;
    }
}
