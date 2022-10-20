<?php

namespace App\Entity;

use App\Repository\EnvoisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnvoisRepository::class)]
class Envois
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'envois')]
    private ?Etape $etape = null;

    #[ORM\ManyToOne(inversedBy: 'envois')]
    private ?Clients $client = null;

    #[ORM\Column(nullable: true)]
    private ?bool $envoyes = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $date = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $heure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtape(): ?Etape
    {
        return $this->etape;
    }

    public function setEtape(?Etape $etape): self
    {
        $this->etape = $etape;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function isEnvoyes(): ?bool
    {
        return $this->envoyes;
    }

    public function setEnvoyes(?bool $envoyes): self
    {
        $this->envoyes = $envoyes;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(?string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }
}
