<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['watch:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['watch:read'])]
    private ?int $watchStock = null;

    #[ORM\OneToOne(inversedBy: 'stock', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Watch $watch = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWatchStock(): ?int
    {
        return $this->watchStock;
    }

    public function setWatchStock(int $watchStock): static
    {
        $this->watchStock = $watchStock;

        return $this;
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
}
