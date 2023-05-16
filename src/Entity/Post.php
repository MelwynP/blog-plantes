<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: "datetime")]
    private \DateTime $publishedAt;


    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'posts', targetEntity: Image::class, orphanRemoval: true, cascade: ['persist'])]
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt()
    {
      return $this->publishedAt;
    }

    public function setPublishedAt($publishedAt)
    {
      $this->publishedAt = $publishedAt;

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
      $image->setPost($this);
    }

    return $this;
  }

  public function removeImage(Image $image): self
  {
    if ($this->image->removeElement($image)) {
      // set the owning side to null (unless already changed)
      if ($image->getPost() === $this) {
        $image->setPost(null);
      }
    }

    return $this;
  }
}
