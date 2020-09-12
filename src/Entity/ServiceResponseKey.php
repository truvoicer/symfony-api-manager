<?php

namespace App\Entity;

use App\Repository\ServiceResponseKeyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass=ServiceResponseKeyRepository::class)
 */
class ServiceResponseKey
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
    private $key_name;

    /**
     * @Groups({"main", "main_relations"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $key_value;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="serviceResponseKey")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\OneToMany(targetEntity=ServiceRequestResponseKey::class, mappedBy="service_response_key", orphanRemoval=true)
     */
    private $serviceRequestResponseKeys;

    public function __construct()
    {
        $this->serviceRequestResponseKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyName(): ?string
    {
        return $this->key_name;
    }

    public function setKeyName(string $key_name): self
    {
        $this->key_name = $key_name;

        return $this;
    }

    public function getKeyValue(): ?string
    {
        return $this->key_value;
    }

    public function setKeyValue(string $key_value): self
    {
        $this->key_value = $key_value;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection|ServiceRequestResponseKey[]
     */
    public function getServiceRequestResponseKeys(): Collection
    {
        return $this->serviceRequestResponseKeys;
    }

    public function addServiceRequestResponseKey(ServiceRequestResponseKey $serviceRequestResponseKey): self
    {
        if (!$this->serviceRequestResponseKeys->contains($serviceRequestResponseKey)) {
            $this->serviceRequestResponseKeys[] = $serviceRequestResponseKey;
            $serviceRequestResponseKey->setServiceResponseKey($this);
        }

        return $this;
    }

    public function removeServiceRequestResponseKey(ServiceRequestResponseKey $serviceRequestResponseKey): self
    {
        if ($this->serviceRequestResponseKeys->contains($serviceRequestResponseKey)) {
            $this->serviceRequestResponseKeys->removeElement($serviceRequestResponseKey);
            // set the owning side to null (unless already changed)
            if ($serviceRequestResponseKey->getServiceResponseKey() === $this) {
                $serviceRequestResponseKey->setServiceResponseKey(null);
            }
        }

        return $this;
    }
}
