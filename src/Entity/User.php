<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Groups({"main", "main_relations"})
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @Groups({"main", "main_relations"})
     * @Assert\NotBlank
     * @Assert\Type("array")
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="datetime")
     */
    private $date_updated;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\Column(type="datetime")
     */
    private $date_added;

    /**
     * @Groups({"main", "main_relations"})
     * @ORM\OneToMany(targetEntity="App\Entity\ApiToken", mappedBy="user", orphanRemoval=true)
     */
    private $apiTokens;


    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * @return Collection|ApiToken[]
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens[] = $apiToken;
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }
}
