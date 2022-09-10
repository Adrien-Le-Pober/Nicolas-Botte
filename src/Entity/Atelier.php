<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
#[Vich\Uploadable]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre est limité à 255 caractères',
    )]
    private $title;

    #[ORM\Column(type: 'datetime')]
    private $date;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $editedAt;

    #[ORM\Column(type: 'string', length: 160, nullable: true)]
    #[Assert\Length(
        max: 160,
        maxMessage: 'La description courte est limitée à {{ limit }} caractères',
    )]
    private $shortDescription;

    #[ORM\Column(type: 'text', nullable: true)]
    private $longDescription;

    #[ORM\Column(type: 'float')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "La valeur ne peut être inférieure à 0"
    )]
    private $price;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'ateliers')]
    #[ORM\JoinColumn(nullable: false, name: "service_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $service;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[Vich\UploadableField(mapping: "ateliers", fileNameProperty : "image")]
    #[Assert\File(
        maxSize : "1M",
        mimeTypes : ["image/jpeg", "image/png", "image/webp"],
        maxSizeMessage : "Le fichier est trop volumineux ({{ size }}{{ suffix }}). La taille maximale est {{ limit }}{{ suffix }}.",
        mimeTypesMessage : "Les fichiers de type {{ type }} ne sont pas supportés, les types supportés sont {{ types }}."
    )]
    #[Assert\NotBlank(message: "Ajoutez une image")]
    private $filePath;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'ateliers')]
    private $orders;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "La valeur ne peut être inférieure à 0"
    )]
    private $stock;

    #[ORM\ManyToOne(targetEntity: Tva::class, inversedBy: 'atelier')]
    #[ORM\JoinColumn(nullable: false)]
    private $tva;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTimeImmutable $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function setFilePath(File $image = null): Atelier
    {
        $this->filePath = $image;
        if ($image) {
            $this->editedAt = new \DateTimeImmutable('now');
        }
        return $this;
    }

    public function getFilePath(): ?File
    {
        return $this->filePath;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addAtelier($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeAtelier($this);
        }

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

        return $this;
    }
}
