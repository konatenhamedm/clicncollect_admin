<?php

namespace App\Entity;

use App\Repository\BaniereRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as Group;


#[ORM\Entity(repositoryClass: BaniereRepository::class)]
class Baniere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Group(["groupe_marque"])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    #[Group(["groupe_marque"])]
    private ?Fichier $photo = null;



    #[ORM\Column(length: 255)]
    #[Group(["groupe_marque"])]
    private ?string $libelle = null;

    #[ORM\Column]
    #[Group(["groupe_marque"])]
    private ?bool $active = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?Fichier
    {
        return $this->photo;
    }

    public function setPhoto(?Fichier $photo): static
    {
        $this->photo = $photo;

        return $this;
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
