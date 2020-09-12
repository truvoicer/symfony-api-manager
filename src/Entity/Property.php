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
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 */
class Property
{
    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main", "main_relations"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $property_name;

    /**
     * @Groups({"main", "main_relations"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $property_label;

    /**
     * @Groups({"main_relations"})
     * @ORM\OneToMany(targetEntity="App\Entity\ProviderProperty", mappedBy="property", orphanRemoval=true)
     */
    private $providerProperties;

    public function __construct()
    {
        $this->providerProperties = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropertyName(): ?string
    {
        return $this->property_name;
    }

    public function setPropertyName(string $property_name): self
    {
        $this->property_name = $property_name;

        return $this;
    }

    public function getPropertyLabel(): ?string
    {
        return $this->property_label;
    }

    public function setPropertyLabel(string $property_label): self
    {
        $this->property_label = $property_label;

        return $this;
    }

    public function toArray()
    {
        return [
            "id" => $this->getId(),
            "property_name" => $this->getPropertyName(),
            "property_label" => $this->getPropertyLabel()
        ];
    }

    /**
     * @return Collection|ProviderProperty[]
     */
    public function getProviderProperties(): Collection
    {
        return $this->providerProperties;
    }

    public function addProviderProperty(ProviderProperty $providerProperty): self
    {
        if (!$this->providerProperties->contains($providerProperty)) {
            $this->providerProperties[] = $providerProperty;
            $providerProperty->setProperty($this);
        }

        return $this;
    }

    public function removeProviderProperty(ProviderProperty $providerProperty): self
    {
        if ($this->providerProperties->contains($providerProperty)) {
            $this->providerProperties->removeElement($providerProperty);
            // set the owning side to null (unless already changed)
            if ($providerProperty->getProperty() === $this) {
                $providerProperty->setProperty(null);
            }
        }

        return $this;
    }
}
