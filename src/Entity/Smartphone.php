<?php

namespace App\Entity;

use App\Repository\SmartphoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmartphoneRepository::class)]
class Smartphone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $systeme = null;

    #[ORM\Column(length: 255)]
    private ?string $stockage = null;

    #[ORM\Column(length: 255)]
    private ?string $ram = null;

    #[ORM\Column(length: 255)]
    private ?string $taille_ecran = null;

    #[ORM\Column(length: 255)]
    private ?string $reseau = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $prix = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSysteme(): ?string
    {
        return $this->systeme;
    }

    public function setSysteme(string $systeme): self
    {
        $this->systeme = $systeme;

        return $this;
    }

    public function getStockage(): ?string
    {
        return $this->stockage;
    }

    public function setStockage(string $stockage): self
    {
        $this->stockage = $stockage;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(string $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getTailleEcran(): ?string
    {
        return $this->taille_ecran;
    }

    public function setTailleEcran(string $taille_ecran): self
    {
        $this->taille_ecran = $taille_ecran;

        return $this;
    }

    public function getReseau(): ?string
    {
        return $this->reseau;
    }

    public function setReseau(string $reseau): self
    {
        $this->reseau = $reseau;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
