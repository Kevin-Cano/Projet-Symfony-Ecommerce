<?php

namespace App\Entity;

use App\Repository\WatchRepository;
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

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $price = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['watch:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $movement = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $material = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $waterResistance = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $bracelet = null;

    #[ORM\Column(length: 255)]
    #[Groups(['watch:read'])]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['watch:read'])]
    private int $stock = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['watch:read'])]
    private bool $isAvailable = true;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
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

    public function getMovement(): ?string
    {
        return $this->movement;
    }

    public function setMovement(string $movement): static
    {
        $this->movement = $movement;
        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(string $material): static
    {
        $this->material = $material;
        return $this;
    }

    public function getWaterResistance(): ?string
    {
        return $this->waterResistance;
    }

    public function setWaterResistance(string $waterResistance): static
    {
        $this->waterResistance = $waterResistance;
        return $this;
    }

    public function getBracelet(): ?string
    {
        return $this->bracelet;
    }

    public function setBracelet(string $bracelet): static
    {
        $this->bracelet = $bracelet;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        $this->isAvailable = ($stock > 0);
        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;
        return $this;
    }

    public function addToStock(int $quantity): self
    {
        $this->stock += $quantity;
        $this->isAvailable = ($this->stock > 0);
        return $this;
    }

    public function removeFromStock(int $quantity): self
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->isAvailable = ($this->stock > 0);
        }
        return $this;
    }
}
