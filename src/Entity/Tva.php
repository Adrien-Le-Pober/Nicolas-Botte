<?php

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TvaRepository::class)]
class Tva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $value;

    #[ORM\OneToMany(mappedBy: 'tva', targetEntity: Atelier::class)]
    private $atelier;

    public function __construct()
    {
        $this->atelier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, Atelier>
     */
    public function getAtelier(): Collection
    {
        return $this->atelier;
    }

    public function addAtelier(Atelier $atelier): self
    {
        if (!$this->atelier->contains($atelier)) {
            $this->atelier[] = $atelier;
            $atelier->setTva($this);
        }

        return $this;
    }

    public function removeAtelier(Atelier $atelier): self
    {
        if ($this->atelier->removeElement($atelier)) {
            // set the owning side to null (unless already changed)
            if ($atelier->getTva() === $this) {
                $atelier->setTva(null);
            }
        }

        return $this;
    }
}
