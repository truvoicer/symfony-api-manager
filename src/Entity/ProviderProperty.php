<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderPropertyRepository")
 */
class ProviderProperty
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="providerProperties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Property", inversedBy="providerProperties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255)
     */
    private $property_value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getPropertyValue(): ?string
    {
        return $this->property_value;
    }

    public function setPropertyValue(string $property_value): self
    {
        $this->property_value = $property_value;

        return $this;
    }
}
