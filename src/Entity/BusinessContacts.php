<?php

namespace App\Entity;

use App\Repository\BusinessContactsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\BusinessContacts;

/**
 * @ORM\Entity(repositoryClass=BusinessContactsRepository::class)
 */
class BusinessContacts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $landline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $company;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AddressStreet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AddressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AddressPostCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AddressCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $BusinessOrPerson;

    /**
     * @ORM\ManyToOne(targetEntity=BusinessTypes::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $businessType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $files;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressCounty;



    /**
     * @ORM\OneToMany(targetEntity=Referrals::class, mappedBy="businessContact", cascade={"remove"}, orphanRemoval=true)
     */
    private $referrals;



    public function getId(): ?int
    {
        return $this->id;
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

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getLandline(): ?string
    {
        return $this->landline;
    }

    public function setLandline(?string $landline): self
    {
        $this->landline = $landline;

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



    public function getAddressStreet(): ?string
    {
        return $this->AddressStreet;
    }

    public function setAddressStreet(?string $AddressStreet): self
    {
        $this->AddressStreet = $AddressStreet;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->AddressCity;
    }

    public function setAddressCity(?string $AddressCity): self
    {
        $this->AddressCity = $AddressCity;

        return $this;
    }

    public function getAddressPostCode(): ?string
    {
        return $this->AddressPostCode;
    }

    public function setAddressPostCode(?string $AddressPostCode): self
    {
        $this->AddressPostCode = $AddressPostCode;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->AddressCountry;
    }

    public function setAddressCountry(?string $AddressCountry): self
    {
        $this->AddressCountry = $AddressCountry;

        return $this;
    }

    public function getBusinessOrPerson(): ?string
    {
        return $this->BusinessOrPerson;
    }

    public function setBusinessOrPerson(?string $BusinessOrPerson): self
    {
        $this->BusinessOrPerson = $BusinessOrPerson;

        return $this;
    }

    public function getBusinessType(): ?BusinessTypes
    {
        return $this->businessType;
    }

    public function setBusinessType(?BusinessTypes $businessType): self
    {
        $this->businessType = $businessType;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLocationLatitude(): ?float
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(?float $locationLatitude): self
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?float
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(?float $locationLongitude): self
    {
        $this->locationLongitude = $locationLongitude;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getFiles(): ?string
    {
        return $this->files;
    }

    public function setFiles(?string $files): self
    {
        $this->files = $files;

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

    public function getAddressCounty(): ?string
    {
        return $this->addressCounty;
    }

    public function setAddressCounty(?string $addressCounty): self
    {
        $this->addressCounty = $addressCounty;

        return $this;
    }



    public function __construct()
    {
        $this->referrals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getReferrals()
    {
        return $this->referrals;
    }

    public function addReferral(Referrals $referral): self
    {
        if (!$this->referrals->contains($referral)) {
            $this->referrals[] = $referral;
            $referral->setBusinessContact($this);
        }
        return $this;
    }

    public function removeReferral(Referrals $referral): self
    {
        if ($this->referrals->removeElement($referral)) {
            // Set the owning side to null
            if ($referral->getBusinessContact() === $this) {
                $referral->setBusinessContact(null);
            }
        }
        return $this;
    }


}
