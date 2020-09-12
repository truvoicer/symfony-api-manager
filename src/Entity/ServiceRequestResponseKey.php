<?php

namespace App\Entity;

use App\Repository\ServiceRequestResponseKeyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ServiceRequestResponseKeyRepository::class)
 */
class ServiceRequestResponseKey
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
     * @ORM\ManyToOne(targetEntity=ServiceRequest::class, inversedBy="serviceRequestResponseKeys", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service_request;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\ManyToOne(targetEntity=ServiceResponseKey::class, inversedBy="serviceRequestResponseKeys", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service_response_key;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255)
     */
    private $response_key_value;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="boolean")
     */
    private $show_in_response;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="boolean")
     */
    private $list_item;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="boolean")
     */
    private $has_array_value;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $array_keys = [];

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $return_data_type;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="boolean")
     */
    private $prepend_extra_data;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255)
     */
    private $prepend_extra_data_value;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="boolean")
     */
    private $append_extra_data;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="string", length=255)
     */
    private $append_extra_data_value;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getServiceResponseKey(): ?ServiceResponseKey
    {
        return $this->service_response_key;
    }

    public function setServiceResponseKey(?ServiceResponseKey $service_response_key): self
    {
        $this->service_response_key = $service_response_key;

        return $this;
    }

    public function getResponseKeyValue(): ?string
    {
        return $this->response_key_value;
    }

    public function setResponseKeyValue(string $response_key_value): self
    {
        $this->response_key_value = $response_key_value;

        return $this;
    }

    public function getShowInResponse(): ?bool
    {
        return $this->show_in_response;
    }

    public function setShowInResponse(bool $show_in_response): self
    {
        $this->show_in_response = $show_in_response;

        return $this;
    }

    public function getListItem(): ?bool
    {
        return $this->list_item;
    }

    public function setListItem(bool $list_item): self
    {
        $this->list_item = $list_item;

        return $this;
    }

    public function getHasArrayValue(): ?bool
    {
        return $this->has_array_value;
    }

    public function setHasArrayValue(bool $has_array_value): self
    {
        $this->has_array_value = $has_array_value;

        return $this;
    }

    public function getArrayKeys(): ?array
    {
        return $this->array_keys;
    }

    public function setArrayKeys(?array $array_keys): self
    {
        $this->array_keys = $array_keys;

        return $this;
    }

    public function getReturnDataType()
    {
        return $this->return_data_type;
    }

    public function setReturnDataType($return_data_type): self
    {
        $this->return_data_type = $return_data_type;

        return $this;
    }

    public function getPrependExtraData(): ?bool
    {
        return $this->prepend_extra_data;
    }

    public function setPrependExtraData(bool $prepend_extra_data): self
    {
        $this->prepend_extra_data = $prepend_extra_data;

        return $this;
    }

    public function getPrependExtraDataValue(): ?string
    {
        return $this->prepend_extra_data_value;
    }

    public function setPrependExtraDataValue(string $prepend_extra_data_value): self
    {
        $this->prepend_extra_data_value = $prepend_extra_data_value;

        return $this;
    }

    public function getAppendExtraData(): ?bool
    {
        return $this->append_extra_data;
    }

    public function setAppendExtraData(bool $append_extra_data): self
    {
        $this->append_extra_data = $append_extra_data;

        return $this;
    }

    public function getAppendExtraDataValue(): ?string
    {
        return $this->append_extra_data_value;
    }

    public function setAppendExtraDataValue(string $append_extra_data_value): self
    {
        $this->append_extra_data_value = $append_extra_data_value;

        return $this;
    }
}
