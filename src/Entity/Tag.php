<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: Action::class)]
    private Collection $actions;

    #[ORM\Column(nullable: true)]
    private ?int $inscrits_aujourdhui = null;

    #[ORM\Column(nullable: true)]
    private ?int $inscrits_hier = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_inscrits = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_desinscrits = null;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: Contact::class)]
    private Collection $contacts;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->contacts = new ArrayCollection();
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

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setTag($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getTag() === $this) {
                $action->setTag(null);
            }
        }

        return $this;
    }

    public function getInscritsAujourdhui(): ?int
    {
        return $this->inscrits_aujourdhui;
    }

    public function setInscritsAujourdhui(?int $inscrits_aujourdhui): self
    {
        $this->inscrits_aujourdhui = $inscrits_aujourdhui;

        return $this;
    }

    public function getInscritsHier(): ?int
    {
        return $this->inscrits_hier;
    }

    public function setInscritsHier(?int $inscrits_hier): self
    {
        $this->inscrits_hier = $inscrits_hier;

        return $this;
    }

    public function getTotalInscrits(): ?int
    {
        return $this->total_inscrits;
    }

    public function setTotalInscrits(?int $total_inscrits): self
    {
        $this->total_inscrits = $total_inscrits;

        return $this;
    }

    public function getTotalDesinscrits(): ?int
    {
        return $this->total_desinscrits;
    }

    public function setTotalDesinscrits(?int $total_desinscrits): self
    {
        $this->total_desinscrits = $total_desinscrits;

        return $this;
    }

    public function __toString()
    {
        return $this->name." ".$this->inscrits_aujourdhui." ".
               $this->inscrits_hier." ".$this->total_inscrits." ".
               $this->total_desinscrits;
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
            $contact->setTag($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getTag() === $this) {
                $contact->setTag(null);
            }
        }

        return $this;
    }
}
