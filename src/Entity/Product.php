<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = null;

    #[ORM\ManyToOne(inversedBy: 'productid')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

 

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: images::class)]
    private Collection $imageid;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: notes::class)]
    private Collection $notes;

    public function __construct()
    {
        
        $this->imageid = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

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

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }


   

    /**
     * @return Collection<int, images>
     */
    public function getImageid(): Collection
    {
        return $this->imageid;
    }

    public function addImageid(images $imageid): static
    {
        if (!$this->imageid->contains($imageid)) {
            $this->imageid->add($imageid);
            $imageid->setProduct($this);
        }

        return $this;
    }

    public function removeImageid(images $imageid): static
    {
        if ($this->imageid->removeElement($imageid)) {
            // set the owning side to null (unless already changed)
            if ($imageid->getProduct() === $this) {
                $imageid->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, notes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(notes $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setProduct($this);
        }

        return $this;
    }

    public function removeNote(notes $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getProduct() === $this) {
                $note->setProduct(null);
            }
        }

        return $this;
    }
}
