<?php

namespace App\Entity;

use App\Repository\ServiceRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass=ServiceRequestRepository::class)
 */
class ServiceRequest
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
    private $service_request_name;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $service_request_label;

    /**
     * @Groups({"main", "main_relations", "single"})
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="serviceRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @Groups({"main", "main_relations", "search", "single"})
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="serviceRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity=ServiceRequestParameter::class, mappedBy="service_request", orphanRemoval=true)
     */
    private $serviceRequestParameters;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity=ServiceRequestConfig::class, mappedBy="service_request", orphanRemoval=true)
     */
    private $serviceRequestConfigs;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity=ServiceRequestResponseKey::class, mappedBy="service_request", orphanRemoval=true)
     */
    private $serviceRequestResponseKeys;

    public function __construct()
    {
        $this->serviceRequestParameters = new ArrayCollection();
        $this->serviceRequestConfigs = new ArrayCollection();
        $this->serviceRequestResponseKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceRequestName(): ?string
    {
        return $this->service_request_name;
    }

    public function setServiceRequestName(string $service_request_name): self
    {
        $this->service_request_name = $service_request_name;

        return $this;
    }

    public function getServiceRequestLabel(): ?string
    {
        return $this->service_request_label;
    }

    public function setServiceRequestLabel(string $service_request_label): self
    {
        $this->service_request_label = $service_request_label;

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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection|ServiceRequestParameter[]
     */
    public function getServiceRequestParameters(): Collection
    {
        return $this->serviceRequestParameters;
    }

    public function addServiceRequestParameter(ServiceRequestParameter $serviceRequestParameter): self
    {
        if (!$this->serviceRequestParameters->contains($serviceRequestParameter)) {
            $this->serviceRequestParameters[] = $serviceRequestParameter;
            $serviceRequestParameter->setServiceRequest($this);
        }

        return $this;
    }

    public function removeServiceRequestParameter(ServiceRequestParameter $serviceRequestParameter): self
    {
        if ($this->serviceRequestParameters->contains($serviceRequestParameter)) {
            $this->serviceRequestParameters->removeElement($serviceRequestParameter);
            // set the owning side to null (unless already changed)
            if ($serviceRequestParameter->getServiceRequest() === $this) {
                $serviceRequestParameter->setServiceRequest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServiceRequestConfig[]
     */
    public function getServiceRequestConfigs(): Collection
    {
        return $this->serviceRequestConfigs;
    }

    public function addServiceRequestConfig(ServiceRequestConfig $serviceRequestConfig): self
    {
        if (!$this->serviceRequestConfigs->contains($serviceRequestConfig)) {
            $this->serviceRequestConfigs[] = $serviceRequestConfig;
            $serviceRequestConfig->setServiceRequest($this);
        }

        return $this;
    }

    public function removeServiceRequestConfig(ServiceRequestConfig $serviceRequestConfig): self
    {
        if ($this->serviceRequestConfigs->contains($serviceRequestConfig)) {
            $this->serviceRequestConfigs->removeElement($serviceRequestConfig);
            // set the owning side to null (unless already changed)
            if ($serviceRequestConfig->getServiceRequest() === $this) {
                $serviceRequestConfig->setServiceRequest(null);
            }
        }

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
            $serviceRequestResponseKey->setServiceRequest($this);
        }

        return $this;
    }

    public function removeServiceRequestResponseKey(ServiceRequestResponseKey $serviceRequestResponseKey): self
    {
        if ($this->serviceRequestResponseKeys->contains($serviceRequestResponseKey)) {
            $this->serviceRequestResponseKeys->removeElement($serviceRequestResponseKey);
            // set the owning side to null (unless already changed)
            if ($serviceRequestResponseKey->getServiceRequest() === $this) {
                $serviceRequestResponseKey->setServiceRequest(null);
            }
        }

        return $this;
    }
}
