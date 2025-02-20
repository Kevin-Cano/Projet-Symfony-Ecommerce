<?php

namespace App\Entity;

use App\Repository\WatchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WatchRepository::class)]
class Watch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['watch:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['watch:read'])]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\ManyToOne(inversedBy: 'watches')]
    #[Groups(['watch:read'])]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $state = null;

    #[ORM\Column]
    #[Groups(['watch:read'])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $picture = null;

    #[ORM\OneToOne(mappedBy: 'watch', cascade: ['persist', 'remove'])]
    #[Groups(['watch:read'])]
    private ?Stock $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $movement = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $material = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $waterResistance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['watch:read'])]
    private ?string $bracelet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): static
    {
        // set the owning side of the relation if necessary
        if ($stock->getWatch() !== $this) {
            $stock->setWatch($this);
        }

        $this->stock = $stock;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMovement(): ?string
    {
        return $this->movement;
    }

    public function setMovement(?string $movement): static
    {
        $this->movement = $movement;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getWaterResistance(): ?string
    {
        return $this->waterResistance;
    }

    public function setWaterResistance(?string $waterResistance): static
    {
        $this->waterResistance = $waterResistance;

        return $this;
    }

    public function getBracelet(): ?string
    {
        return $this->bracelet;
    }

    public function setBracelet(?string $bracelet): static
    {
        $this->bracelet = $bracelet;

        return $this;
    }
}
