<?php

namespace App\Entity;

use App\Repository\DiscoverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscoverRepository::class)]
class Discover
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $language = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $population = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $area = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contentIntro = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contentFooter = null;

    #[ORM\OneToMany(mappedBy: 'discover', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]
    private $image;

  public function __construct()
  {
    $this->image = new ArrayCollection();
  }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPopulation(): ?string
    {
        return $this->population;
    }

    public function setPopulation(?string $population): self
    {
        $this->population = $population;

        return $this;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getContentIntro(): ?string
    {
        return $this->contentIntro;
    }

    public function setContentIntro(?string $contentIntro): self
    {
        $this->contentIntro = $contentIntro;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContentFooter(): ?string
    {
        return $this->contentFooter;
    }

    public function setContentFooter(?string $contentFooter): self
    {
        $this->contentFooter = $contentFooter;

        return $this;
    }

    public function getImage(): Collection
  {
    return $this->image;
  }
  
    public function addImage(Image $image): self
  {
    if (!$this->image->contains($image)) {
      $this->image[] = $image;
      $image->setDiscover($this);
    }

    return $this;
  }

  public function removeImage(Image $image): self
  {
    if ($this->image->removeElement($image)) {
      // set the owning side to null (unless already changed)
      if ($image->getDiscover() === $this) {
        $image->setDiscover(null);
      }
    }

    return $this;
  }
}
