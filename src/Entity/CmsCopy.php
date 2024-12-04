<?php

namespace App\Entity;

use App\Repository\CmsCopyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsCopyRepository::class)
 */
class CmsCopy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hyperlinks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contentTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentTextFR;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contentTextDE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contentTitleFR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contentTitleDE;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tabTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $staticPageName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ranking;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tabTitleFR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tabTitleDE;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pageCountUsers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pageCountAdmin;

    /**
     * @ORM\ManyToOne(targetEntity=CmsCopyPageFormats::class)
     */
    private $pageLayout;




    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContentText(): ?string
    {
        return $this->contentText;
    }

    public function setContentText(string $contentText): self
    {
        $this->contentText = $contentText;

        return $this;
    }

    public function getHyperlinks(): ?string
    {
        return $this->hyperlinks;
    }

    public function setHyperlinks(?string $hyperlinks): self
    {
        $this->hyperlinks = $hyperlinks;

        return $this;
    }

    public function getContentTitle(): ?string
    {
        return $this->contentTitle;
    }

    public function setContentTitle(?string $contentTitle): self
    {
        $this->contentTitle = $contentTitle;

        return $this;
    }

    public function getContentTextFR(): ?string
    {
        return $this->contentTextFR;
    }

    public function setContentTextFR(?string $contentTextFR): self
    {
        $this->contentTextFR = $contentTextFR;

        return $this;
    }

    public function getContentTextDE(): ?string
    {
        return $this->contentTextDE;
    }

    public function setContentTextDE(?string $contentTextDE): self
    {
        $this->contentTextDE = $contentTextDE;

        return $this;
    }

    public function getContentTitleFR(): ?string
    {
        return $this->contentTitleFR;
    }

    public function setContentTitleFR(?string $contentTitleFR): self
    {
        $this->contentTitleFR = $contentTitleFR;

        return $this;
    }

    public function getContentTitleDE(): ?string
    {
        return $this->contentTitleDE;
    }

    public function setContentTitleDE(?string $contentTitleDE): self
    {
        $this->contentTitleDE = $contentTitleDE;

        return $this;
    }



    public function getTabTitle(): ?string
    {
        return $this->tabTitle;
    }

    public function setTabTitle(?string $tabTitle): self
    {
        $this->tabTitle = $tabTitle;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getStaticPageName(): ?string
    {
        return $this->staticPageName;
    }

    public function setStaticPageName(?string $staticPageName): self
    {
        $this->staticPageName = $staticPageName;

        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): self
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function getTabTitleFR(): ?string
    {
        return $this->tabTitleFR;
    }

    public function setTabTitleFR(?string $tabTitleFR): self
    {
        $this->tabTitleFR = $tabTitleFR;

        return $this;
    }

    public function getTabTitleDE(): ?string
    {
        return $this->tabTitleDE;
    }

    public function setTabTitleDE(?string $tabTitleDE): self
    {
        $this->tabTitleDE = $tabTitleDE;

        return $this;
    }

    public function getPageCountUsers(): ?int
    {
        return $this->pageCountUsers;
    }

    public function setPageCountUsers(?int $pageCountUsers): self
    {
        $this->pageCountUsers = $pageCountUsers;

        return $this;
    }

    public function getPageCountAdmin(): ?int
    {
        return $this->pageCountAdmin;
    }

    public function setPageCountAdmin(?int $pageCountAdmin): self
    {
        $this->pageCountAdmin = $pageCountAdmin;

        return $this;
    }

    public function getPageLayout(): ?cmsCopyPageFormats
    {
        return $this->pageLayout;
    }

    public function setPageLayout(?cmsCopyPageFormats $pageLayout): self
    {
        $this->pageLayout = $pageLayout;

        return $this;
    }



}
