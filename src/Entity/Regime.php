<?php

namespace App\Entity;

use App\Repository\RegimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegimeRepository::class)]
class Regime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'regime', targetEntity: DataAnalyse::class)]
    private Collection $dataAnalyses;

    public function __construct()
    {
        $this->dataAnalyses = new ArrayCollection();
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
     * @return Collection<int, DataAnalyse>
     */
    public function getDataAnalyses(): Collection
    {
        return $this->dataAnalyses;
    }

    public function addDataAnalysis(DataAnalyse $dataAnalysis): static
    {
        if (!$this->dataAnalyses->contains($dataAnalysis)) {
            $this->dataAnalyses->add($dataAnalysis);
            $dataAnalysis->setRegime($this);
        }

        return $this;
    }

    public function removeDataAnalysis(DataAnalyse $dataAnalysis): static
    {
        if ($this->dataAnalyses->removeElement($dataAnalysis)) {
            // set the owning side to null (unless already changed)
            if ($dataAnalysis->getRegime() === $this) {
                $dataAnalysis->setRegime(null);
            }
        }

        return $this;
    }
}
