<?php

namespace App\Entity;

use App\Repository\LitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LitRepository::class)]
class Lit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeEmplacement = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $descriptionEmplacement = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'lits')]
    private ?Chambre $chambre = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }

    public function getTypeEmplacement(): ?string
    {
        return $this->typeEmplacement;
    }

    public function setTypeEmplacement(?string $typeEmplacement): static
    {
        $this->typeEmplacement = $typeEmplacement;

        return $this;
    }

    public function getDescriptionEmplacement(): ?string
    {
        return $this->descriptionEmplacement;
    }

    public function setDescriptionEmplacement(?string $descriptionEmplacement): static
    {
        $this->descriptionEmplacement = $descriptionEmplacement;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
