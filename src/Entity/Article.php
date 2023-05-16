<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'articles', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]
    private $image;

    public function __construct()
    {
      $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
        $image->setArticle($this);
      }

      return $this;
    }

    public function removeImage(Image $image): self
    {
      if ($this->image->removeElement($image)) {
        // set the owning side to null (unless already changed)
        if ($image->getArticle() === $this) {
          $image->setArticle(null);
        }
      }

      return $this;
    }
}
