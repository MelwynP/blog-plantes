<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max:400, maxMessage: 'Le contenu doit contenir au maximum {{ limit }} caractères')]

    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    #[ORM\JoinColumn(nullable: true)]

    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
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

    public function getCategory(): ?Category
    {
      return $this->category;
    }

    public function setCategory(?Category $category): self
    {
      $this->category = $category;

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
