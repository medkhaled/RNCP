<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private Collection $productid;

    #[ORM\Column]
    private ?bool $isVerified = null;

    public function __construct()
    {
        $this->productid = new ArrayCollection();
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

    /**
     * @return Collection<int, Product>
     */
    public function getProductid(): Collection
    {
        return $this->productid;
    }
    

    public function addProductid(Product $productid): static
    {
        if (!$this->productid->contains($productid)) {
            $this->productid->add($productid);
            $productid->setCategory($this);
        }

        return $this;
    }

    public function removeProductid(Product $productid): static
    {
        if ($this->productid->removeElement($productid)) {
            // set the owning side to null (unless already changed)
            if ($productid->getCategory() === $this) {
                $productid->setCategory(null);
            }
        }

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
     /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
