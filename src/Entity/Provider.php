<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderRepository")
 */
class Provider
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
     * @ORM\Column(type="string", length=255)
     */
    private $provider_name;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $provider_api_base_url;

    /**
     * @Groups({"main", "main_relations", "list", "single"})
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $provider_access_key;

    /**
     * @Groups({"main", "main_relations", "list", "single"})
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $provider_secret_key;

    /**
     * @Groups({"main", "main_relations", "list", "single"})
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $provider_user_id;

    /**
     * @Groups({"main", "main_relations", "list", "single"})
     * @ORM\Column(type="datetime")
     */
    private $date_updated;

    /**
     * @Groups({"main", "main_relations", "list", "single"})
     * @ORM\Column(type="datetime")
     */
    private $date_added;

    /**
     * @Groups({"main_relations"})
     * @ORM\OneToMany(targetEntity="App\Entity\ProviderProperty", mappedBy="provider", orphanRemoval=true)
     */
    private $providerProperties;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OauthAccessTokens", mappedBy="provider", orphanRemoval=true)
     */
    private $oauthAccessTokens;

    /**
     * @Groups({"main", "main_relations", "search", "single"})
     * @ORM\OneToMany(targetEntity=ServiceRequest::class, mappedBy="provider", orphanRemoval=true)
     */
    private $serviceRequests;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $provider_label;

    /**
     * @Groups({"main", "main_relations", "search", "list", "single"})
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="providers")
     */
    private $category;


    public function __construct()
    {
        $this->providerProperties = new ArrayCollection();
        $this->oauthAccessTokens = new ArrayCollection();
        $this->serviceRequests = new ArrayCollection();
        $this->category = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderName(): ?string
    {
        return $this->provider_name;
    }

    public function setProviderName(string $provider_name): self
    {
        $this->provider_name = strtolower($provider_name);

        return $this;
    }

    public function getProviderApiBaseUrl(): ?string
    {
        return $this->provider_api_base_url;
    }

    public function setProviderApiBaseUrl(string $provider_api_base_url): self
    {
        $this->provider_api_base_url = $provider_api_base_url;

        return $this;
    }

    public function getProviderAccessKey(): ?string
    {
        return $this->provider_access_key;
    }

    public function setProviderAccessKey(string $provider_access_key): self
    {
        $this->provider_access_key = $provider_access_key;

        return $this;
    }

    public function getProviderSecretKey(): ?string
    {
        return $this->provider_secret_key;
    }

    public function setProviderSecretKey(string $provider_secret_key): self
    {
        $this->provider_secret_key = $provider_secret_key;

        return $this;
    }

    public function getProviderUserId(): ?string
    {
        return $this->provider_user_id;
    }

    public function setProviderUserId(string $provider_user_id): self
    {
        $this->provider_user_id = $provider_user_id;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): self
    {
        $this->date_updated = $date_updated;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded(\DateTimeInterface $date_added): self
    {
        $this->date_added = $date_added;

        return $this;
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
            $providerProperty->setProvider($this);
        }

        return $this;
    }

    public function removeProviderProperty(ProviderProperty $providerProperty): self
    {
        if ($this->providerProperties->contains($providerProperty)) {
            $this->providerProperties->removeElement($providerProperty);
            // set the owning side to null (unless already changed)
            if ($providerProperty->getProvider() === $this) {
                $providerProperty->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OauthAccessTokens[]
     */
    public function getOauthAccessTokens(): Collection
    {
        return $this->oauthAccessTokens;
    }

    public function addOauthAccessToken(OauthAccessTokens $oauthAccessToken): self
    {
        if (!$this->oauthAccessTokens->contains($oauthAccessToken)) {
            $this->oauthAccessTokens[] = $oauthAccessToken;
            $oauthAccessToken->setProvider($this);
        }

        return $this;
    }

    public function removeOauthAccessToken(OauthAccessTokens $oauthAccessToken): self
    {
        if ($this->oauthAccessTokens->contains($oauthAccessToken)) {
            $this->oauthAccessTokens->removeElement($oauthAccessToken);
            // set the owning side to null (unless already changed)
            if ($oauthAccessToken->getProvider() === $this) {
                $oauthAccessToken->setProvider(null);
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
            $serviceRequest->setProvider($this);
        }

        return $this;
    }

    public function removeServiceRequest(ServiceRequest $serviceRequest): self
    {
        if ($this->serviceRequests->contains($serviceRequest)) {
            $this->serviceRequests->removeElement($serviceRequest);
            // set the owning side to null (unless already changed)
            if ($serviceRequest->getProvider() === $this) {
                $serviceRequest->setProvider(null);
            }
        }

        return $this;
    }

    public function getProviderLabel(): ?string
    {
        return $this->provider_label;
    }

    public function setProviderLabel(string $provider_label): self
    {
        $this->provider_label = $provider_label;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }
}
