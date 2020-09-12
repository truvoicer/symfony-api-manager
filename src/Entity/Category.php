<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Column(type="string", length=255)
     */
    private $category_name;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Column(type="string", length=255)
     */
    private $category_label;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="category", orphanRemoval=true)
     */
    private $services;

    /**
     * @ORM\ManyToMany(targetEntity=Provider::class, mappedBy="category")
     */
    private $providers;

    public function __construct()
    {
        $this->providers = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->category_name;
    }

    public function setCategoryName(string $category_name): self
    {
        $this->category_name = $category_name;

        return $this;
    }

    public function getCategoryLabel(): ?string
    {
        return $this->category_label;
    }

    public function setCategoryLabel(string $category_label): self
    {
        $this->category_label = $category_label;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getCategory() === $this) {
                $service->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
            $provider->addCategory($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->contains($provider)) {
            $this->providers->removeElement($provider);
            $provider->removeCategory($this);
        }

        return $this;
    }
}
