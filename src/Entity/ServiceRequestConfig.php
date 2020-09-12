<?php

namespace App\Entity;

use App\Repository\ServiceRequestConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass=ServiceRequestConfigRepository::class)
 */
class ServiceRequestConfig
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
    private $item_name;

    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $item_value;

    /**
     * @Groups({"main", "main_relations", "single"})
     * @ORM\ManyToOne(targetEntity=ServiceRequest::class, inversedBy="serviceRequestConfigs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service_request;

    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $value_type;

    /**
     * @Groups({"main", "main_relations", "single", "list"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $item_array_value = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemName(): ?string
    {
        return $this->item_name;
    }

    public function setItemName(string $item_name): self
    {
        $this->item_name = $item_name;

        return $this;
    }

    public function getItemValue(): ?string
    {
        return $this->item_value;
    }

    public function setItemValue(string $item_value): self
    {
        $this->item_value = $item_value;

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

    public function getValueType(): ?string
    {
        return $this->value_type;
    }

    public function setValueType(string $value_type): self
    {
        $this->value_type = $value_type;

        return $this;
    }

    public function getItemArrayValue(): ?array
    {
        return $this->item_array_value;
    }

    public function setItemArrayValue(?array $item_array_value): self
    {
        $this->item_array_value = $item_array_value;

        return $this;
    }
}
