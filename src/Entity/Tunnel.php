<?php

namespace App\Entity;

use App\Repository\TunnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: TunnelRepository::class)]
class Tunnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Length(min=4, minMessage="Veuillez avoir au moins 4 caractères")
     */
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'tunnel', targetEntity: Atterissage::class)]
    private Collection $atterissages;

    public function __construct()
    {
        $this->atterissages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Atterissage>
     */
    public function getAtterissages(): Collection
    {
        return $this->atterissages;
    }

    public function addAtterissage(Atterissage $atterissage): self
    {
        if (!$this->atterissages->contains($atterissage)) {
            $this->atterissages->add($atterissage);
            $atterissage->setTunnel($this);
        }

        return $this;
    }

    public function removeAtterissage(Atterissage $atterissage): self
    {
        if ($this->atterissages->removeElement($atterissage)) {
            // set the owning side to null (unless already changed)
            if ($atterissage->getTunnel() === $this) {
                $atterissage->setTunnel(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name." ".$this->active;
    }
}
