<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $symbol = null;

    #[ORM\OneToOne(mappedBy: 'currency', cascade: ['persist', 'remove'])]
    private ?Country $country = null;

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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): static
    {
        // set the owning side of the relation if necessary
        if ($country->getCurrency() !== $this) {
            $country->setCurrency($this);
        }

        $this->country = $country;

        return $this;
    }
}
