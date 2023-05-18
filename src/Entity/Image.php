<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $path = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'image')]
    #[ORM\JoinColumn(nullable: true)]
    private $post;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'image')]
    #[ORM\JoinColumn(nullable: false)]
    private $article;

    #[ORM\ManyToOne(targetEntity: discover::class, inversedBy: 'image')]
    #[ORM\JoinColumn(nullable: true)]
    private $discover;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getDiscover(): ?discover
    {
        return $this->discover;
    }

    public function setDiscover(?Discover $discover): self
    {
        $this->discover = $discover;

        return $this;
    }


}
