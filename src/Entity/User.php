<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "user")]


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private string $email;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email3 = null;


    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $emailVerified = null;
    /**
     * @ORM\Column(type="json")
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $salutation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mobile = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mobile2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $plainPassword = null;


    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'user', orphanRemoval: true)]
    private $logs;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthday = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $webPage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $jobTitle = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $linkedIn = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $businessPhone = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $businessStreet = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $businessCity = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $businessPostalCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $businessCountry = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homePhone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homePhone2 = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homeStreet = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homeCity = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homePostalCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $homeCountry = null;


    #[ORM\OneToMany(targetEntity: PhotoLocations::class, mappedBy: 'enabledUsers')]
    private $photoLocations;



    #[ORM\ManyToOne]
    private ?Languages $defaultLanguage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'recipient')]
    private ?ClientEmailsSent $clientEmailsSent = null;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(?string $salutation): self
    {
        $this->salutation = $salutation;
        return $this;
    }


    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->lastName = $LastName;
        return $this;
    }


    public function getUserIdentifier(): string
    {
        // Typically, the identifier is the user's email or username
        return $this->email ?? '';
    }

    public function getEmail(): ?string
    {
        return $this->email ?? '';
    }

    public function setEmail(string $email): self
    {
        $this->email = $email ?? '';
        return $this;
    }


    public function getEmail2(): ?string
    {
        return $this->email2;
    }

    public function setEmail2(?string $email2): static
    {
        $this->email2 = $email2;

        return $this;
    }

    public function getEmail3(): ?string
    {
        return $this->email3;
    }

    public function setEmail3(?string $email3): self
    {
        $this->email3 = $email3;
        return $this;
    }

    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // Clear any temporary, sensitive data here
    }

    public function getFullName(): ?string
    {
        if ($this->fullName === null) {
            return $this->getFirstName() . " " . $this->getLastName();
        }
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function getMobile2(): ?string
    {
        return $this->mobile2;
    }

    public function setMobile2(?string $mobile2): self
    {
        $this->mobile2 = $mobile2;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUser($this);
        }
        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->removeElement($log)) {
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getBusinessPhone(): ?string
    {
        return $this->businessPhone;
    }

    public function setBusinessPhone(?string $businessPhone): self
    {
        $this->businessPhone = $businessPhone;
        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function setHomePhone(?string $homePhone): self
    {
        $this->homePhone = $homePhone;
        return $this;
    }

    public function getHomePhone2(): ?string
    {
        return $this->homePhone2;
    }

    public function setHomePhone2(?string $homePhone2): self
    {
        $this->homePhone2 = $homePhone2;
        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function getWebPage(): ?string
    {
        return $this->webPage;
    }

    public function setWebPage(?string $webPage): self
    {
        $this->webPage = $webPage;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }




    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;
        return $this;
    }

    public function getBusinessStreet(): ?string
    {
        return $this->businessStreet;
    }

    public function setBusinessStreet(?string $businessStreet): self
    {
        $this->businessStreet = $businessStreet;

        return $this;
    }

    public function getBusinessCity(): ?string
    {
        return $this->businessCity;
    }

    public function setBusinessCity(?string $businessCity): self
    {
        $this->businessCity = $businessCity;

        return $this;
    }

    public function getBusinessPostalCode(): ?string
    {
        return $this->businessPostalCode;
    }

    public function setBusinessPostalCode(?string $businessPostalCode): self
    {
        $this->businessPostalCode = $businessPostalCode;

        return $this;
    }

    public function getBusinessCountry(): ?string
    {
        return $this->businessCountry;
    }

    public function setBusinessCountry(?string $businessCountry): self
    {
        $this->businessCountry = $businessCountry;

        return $this;
    }

    public function getHomeStreet(): ?string
    {
        return $this->homeStreet;
    }

    public function setHomeStreet(?string $homeStreet): self
    {
        $this->homeStreet = $homeStreet;

        return $this;
    }

    public function getHomeCity(): ?string
    {
        return $this->homeCity;
    }

    public function setHomeCity(?string $homeCity): self
    {
        $this->homeCity = $homeCity;

        return $this;
    }

    public function getHomePostalCode(): ?string
    {
        return $this->homePostalCode;
    }

    public function setHomePostalCode(?string $homePostalCode): self
    {
        $this->homePostalCode = $homePostalCode;

        return $this;
    }

    public function getHomeCountry(): ?string
    {
        return $this->homeCountry;
    }

    public function setHomeCountry(?string $homeCountry): self
    {
        $this->homeCountry = $homeCountry;

        return $this;
    }

    /**
     * @return Collection|PhotoLocations[]
     */
    public function getPhotoLocations(): Collection
    {
        return $this->photoLocations;
    }

    public function addPhotoLocation(PhotoLocations $photoLocation): self
    {
        if (!$this->photoLocations->contains($photoLocation)) {
            $this->photoLocations[] = $photoLocation;
            $photoLocation->setEnabledUsers($this);
        }

        return $this;
    }

    public function removePhotoLocation(PhotoLocations $photoLocation): self
    {
        if ($this->photoLocations->removeElement($photoLocation)) {
            // set the owning side to null (unless already changed)
            if ($photoLocation->getEnabledUsers() === $this) {
                $photoLocation->setEnabledUsers(null);
            }
        }

        return $this;
    }




    public function getEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(?bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }


    public function getDefaultLanguage(): ?Languages
    {
        return $this->defaultLanguage;
    }

    public function setDefaultLanguage(?Languages $defaultLanguage): static
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getClientEmailsSent(): ?ClientEmailsSent
    {
        return $this->clientEmailsSent;
    }

    public function setClientEmailsSent(?ClientEmailsSent $clientEmailsSent): static
    {
        $this->clientEmailsSent = $clientEmailsSent;

        return $this;
    }

}
