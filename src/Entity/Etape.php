<?php

namespace App\Entity;

use App\Repository\EtapeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapeRepository::class)]
class Etape
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?int $delai = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $temps = null;

    #[ORM\ManyToOne(inversedBy: 'etapes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ordre = null;

    #[ORM\OneToMany(mappedBy: 'etape', targetEntity: Envois::class)]
    private Collection $envois;

    #[ORM\Column(nullable: true)]
    private ?int $email_envoyes = null;

    #[ORM\Column(nullable: true)]
    private ?int $pre_etape = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    public function __construct()
    {
        $this->envois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDelai(): ?int
    {
        return $this->delai;
    }

    public function setDelai(?int $delai): self
    {
        $this->delai = $delai;

        return $this;
    }

    public function getTemps(): ?string
    {
        return $this->temps;
    }

    public function setTemps(?string $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): self
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getOrdre(): ?string
    {
        return $this->ordre;
    }

    public function setOrdre(?string $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection<int, Envois>
     */
    public function getEnvois(): Collection
    {
        return $this->envois;
    }

    public function addEnvoi(Envois $envoi): self
    {
        if (!$this->envois->contains($envoi)) {
            $this->envois->add($envoi);
            $envoi->setEtape($this);
        }

        return $this;
    }

    public function removeEnvoi(Envois $envoi): self
    {
        if ($this->envois->removeElement($envoi)) {
            // set the owning side to null (unless already changed)
            if ($envoi->getEtape() === $this) {
                $envoi->setEtape(null);
            }
        }

        return $this;
    }

    public function getEmailEnvoyes(): ?int
    {
        return $this->email_envoyes;
    }

    public function setEmailEnvoyes(?int $email_envoyes): self
    {
        $this->email_envoyes = $email_envoyes;

        return $this;
    }

    public function __toString()
    {
        return $this->campagne." ".$this->user." ".
               $this->email." ".$this->sujet." ".
               $this->message." ".$this->delai." ".
               $this->temps." ";
    }

    public function getPreEtape(): ?int
    {
        return $this->pre_etape;
    }

    public function setPreEtape(?int $pre_etape): self
    {
        $this->pre_etape = $pre_etape;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
