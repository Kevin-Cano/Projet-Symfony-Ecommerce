<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'stock', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Watch $watch = null;

    #[ORM\Column(nullable: true)]
    private ?int $watchStock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWatch(): ?Watch
    {
        return $this->watch;
    }

    public function setWatch(Watch $watch): static
    {
        $this->watch = $watch;

        return $this;
    }

    public function getWatchStock(): ?int
    {
        return $this->watchStock;
    }

    public function setWatchStock(?int $watchStock): static
    {
        $this->watchStock = $watchStock;

        return $this;
    }
}
