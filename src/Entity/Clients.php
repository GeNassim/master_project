<?php

namespace App\Entity;

use App\Repository\ClientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientsRepository::class)]
class Clients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[
        ORM\Column(length: 255),
        Assert\Email(
            message: "{{ value }} n'est pas valide. Veuillez entrer un e-mail valide"
        )
    ]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Atterissage $atterissage = null;

    #[ORM\OneToMany(mappedBy: 'clients', targetEntity: Contact::class)]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Envois::class)]
    private Collection $envois;

    #[ORM\Column(nullable: true)]
    private ?bool $is_verfied = false;

    #[ORM\OneToMany(mappedBy: 'clients', targetEntity: Desabonne::class)]
    private Collection $desabonnes;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->envois = new ArrayCollection();
        $this->desabonnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getAtterissage(): ?Atterissage
    {
        return $this->atterissage;
    }

    public function setAtterissage(?Atterissage $atterissage): self
    {
        $this->atterissage = $atterissage;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setClients($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getClients() === $this) {
                $contact->setClients(null);
            }
        }

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
            $envoi->setClient($this);
        }

        return $this;
    }

    public function removeEnvoi(Envois $envoi): self
    {
        if ($this->envois->removeElement($envoi)) {
            // set the owning side to null (unless already changed)
            if ($envoi->getClient() === $this) {
                $envoi->setClient(null);
            }
        }

        return $this;
    }

    public function isIsVerfied(): ?bool
    {
        return $this->is_verfied;
    }

    public function setIsVerfied(?bool $is_verfied): self
    {
        $this->is_verfied = $is_verfied;

        return $this;
    }

    /**
     * @return Collection<int, Desabonne>
     */
    public function getDesabonnes(): Collection
    {
        return $this->desabonnes;
    }

}
