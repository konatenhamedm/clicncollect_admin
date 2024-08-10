<?php

namespace App\Entity;

use App\Repository\DataAnalyseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataAnalyseRepository::class)]
class DataAnalyse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $nomEtablissement = null;

    #[ORM\Column(length: 5)]
    private ?string $sexeGenre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'dataAnalyses')]
    private ?Statut $statut = null;

    #[ORM\ManyToOne(inversedBy: 'dataAnalyses')]
    private ?Regime $regime = null;

    #[ORM\Column(length: 255)]
    private ?int $total = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(string $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getNomEtablissement(): ?string
    {
        return $this->nomEtablissement;
    }

    public function setNomEtablissement(string $nomEtablissement): static
    {
        $this->nomEtablissement = $nomEtablissement;

        return $this;
    }

    public function getSexeGenre(): ?string
    {
        return $this->sexeGenre;
    }

    public function setSexeGenre(string $sexeGenre): static
    {
        $this->sexeGenre = $sexeGenre;

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

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): static
    {
        $this->regime = $regime;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }
}
