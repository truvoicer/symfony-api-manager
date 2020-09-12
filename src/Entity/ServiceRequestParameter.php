<?php

namespace App\Entity;

use App\Repository\ServiceRequestParameterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass=ServiceRequestParameterRepository::class)
 */
class ServiceRequestParameter
{
    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $parameter_name;

    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $parameter_value;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\ManyToOne(targetEntity=ServiceRequest::class, inversedBy="serviceRequestParameters", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service_request;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParameterName(): ?string
    {
        return $this->parameter_name;
    }

    public function setParameterName(string $parameter_name): self
    {
        $this->parameter_name = $parameter_name;

        return $this;
    }

    public function getParameterValue(): ?string
    {
        return $this->parameter_value;
    }

    public function setParameterValue(string $parameter_value): self
    {
        $this->parameter_value = $parameter_value;

        return $this;
    }

    public function getServiceRequest(): ?ServiceRequest
    {
        return $this->service_request;
    }

    public function setServiceRequest(?ServiceRequest $service_request): self
    {
        $this->service_request = $service_request;

        return $this;
    }
}
