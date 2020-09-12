<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 */
class Service
{
    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $service_name;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $service_label;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity=ServiceResponseKey::class, mappedBy="service", orphanRemoval=true)
     */
    private $serviceResponseKeys;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity=ServiceRequest::class, mappedBy="service", orphanRemoval=true)
     */
    private $serviceRequests;

    public function __construct()
    {
        $this->serviceResponseKeys = new ArrayCollection();
        $this->serviceRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->service_name;
    }

    public function setServiceName(string $service_name): self
    {
        $this->service_name = $service_name;

        return $this;
    }

    public function getServiceLabel(): ?string
    {
        return $this->service_label;
    }

    public function setServiceLabel(string $service_label): self
    {
        $this->service_label = $service_label;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|ServiceResponseKey[]
     */
    public function getServiceResponseKeys(): Collection
    {
        return $this->serviceResponseKeys;
    }

    public function addServiceResponseKey(ServiceResponseKey $serviceResponseKey): self
    {
        if (!$this->serviceResponseKeys->contains($serviceResponseKey)) {
            $this->serviceResponseKeys[] = $serviceResponseKey;
            $serviceResponseKey->setService($this);
        }

        return $this;
    }

    public function removeServiceResponseKey(ServiceResponseKey $serviceResponseKey): self
    {
        if ($this->serviceResponseKeys->contains($serviceResponseKey)) {
            $this->serviceResponseKeys->removeElement($serviceResponseKey);
            // set the owning side to null (unless already changed)
            if ($serviceResponseKey->getService() === $this) {
                $serviceResponseKey->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServiceRequest[]
     */
    public function getServiceRequests(): Collection
    {
        return $this->serviceRequests;
    }

    public function addServiceRequest(ServiceRequest $serviceRequest): self
    {
        if (!$this->serviceRequests->contains($serviceRequest)) {
            $this->serviceRequests[] = $serviceRequest;
            $serviceRequest->setService($this);
        }

        return $this;
    }

    public function removeServiceRequest(ServiceRequest $serviceRequest): self
    {
        if ($this->serviceRequests->contains($serviceRequest)) {
            $this->serviceRequests->removeElement($serviceRequest);
            // set the owning side to null (unless already changed)
            if ($serviceRequest->getService() === $this) {
                $serviceRequest->setService(null);
            }
        }

        return $this;
    }
}
